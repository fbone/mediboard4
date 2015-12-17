<?php

/**
 * $Id$
 *  
 * @category Patient
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CINSPatient extends CMbObject {
  /**
   * @var integer Primary key
   */
  public $ins_patient_id;
  public $patient_id;
  public $ins;
  public $type;
  public $date;
  public $provider;

  /**
   * Initialize the class specifications
   *
   * @return CMbFieldSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "ins_patient";
    $spec->key    = "ins_patient_id";
    return $spec;  
  }
  
  /**
   * Get collections specifications
   *
   * @return array
   */
  function getBackProps() {
    $backProps = parent::getBackProps();

    return $backProps;
  }
  
  /**
   * Get the properties of our class as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();

    $props["patient_id"] = "ref class|CPatient notNull";
    $props["ins"]        = "str notNull";
    $props["type"]       = "enum list|A|C notNull";
    $props["date"]       = "dateTime notNull";
    $props["provider"]   = "str notNull";

    return $props;
  }

  /**
   * Create INSC
   *
   * @param CPatient $patient patient
   *
   * @return null|string
   */
  static function createINSC(CPatient $patient) {
    if (!$patient->_vitale_nir_certifie) {
      return "Ce patient ne poss�de pas de num�ro de s�curit� sociale qui lui est propre";
    }

    list($nir_carte, $nir_carte_key) = explode(" ", $patient->_vitale_nir_certifie);

    $name_carte     = mb_strtoupper(CMbString::removeAccents($patient->_vitale_lastname));
    $prenom_carte   = mb_strtoupper(CMbString::removeAccents($patient->_vitale_firstname));
    $name_patient   = mb_strtoupper(CMbString::removeAccents($patient->nom));
    $prenom_patient = mb_strtoupper(CMbString::removeAccents($patient->prenom));

    if ($name_carte !== $name_patient || $prenom_carte !== $prenom_patient) {
      return "Le b�n�ficiaire de la carte vitale ne correspond pas au patient en cours";
    }

    $firstName = self::formatString($patient->_vitale_firstname);
    $insc      = self::calculInsc($nir_carte, $nir_carte_key, $firstName, $patient->_vitale_birthdate);

    if (strlen($insc) !== 22) {
      return "Probl�me lors du calcul de l'INSC";
    }

    if (!$insc) {
      return "Impossible de calculer l'INSC";
    }

    $last_ins = $patient->loadLastINS();

    if ($last_ins && $last_ins->ins === $insc) {
      return null;
    }

    $ins = new CINSPatient();
    $ins->patient_id = $patient->_id;
    $ins->ins        = $insc;

    $ins->type       = "C";
    $ins->date       = "now";
    $ins->provider   = "Mediboard";


    if ($msg = $ins->store()) {
      return $msg;
    };

    return null;
  }

  /**
   * Formate la chaine pour l'INSC
   *
   * @param String $string String
   *
   * @return String
   */
  static function formatString($string) {
    $String_no_accent = CMbString::removeAccents($string);
    $normalize   = preg_replace("/([^A-Za-z])/", " ", $String_no_accent);
    return mb_strtoupper($normalize);
  }
  /**
   * Calculation the INSC (Use the data of the vital card (clean the data before!!!))
   *
   * @param String $nir        nir certified
   * @param String $nir_key    key nir
   * @param String $first_name firstname
   * @param String $birth_date birth date
   *
   * @return null|string
   */
  static function calculInsc($nir, $nir_key, $first_name = "", $birth_date = "000000") {
    $nir_complet = $nir.$nir_key;

    //on v�rifie que le nir est valide
    if (CCodeSpec::checkInsee($nir_complet)) {
      return null;
    }

    //on v�rifie que le nir n'est pas un nir temporaire
    if (!preg_match("/^([12][0-9]{2}[0-9]{2}[0-9][0-9ab][0-9]{3}[0-9]{3})([0-9]{2})$/i", $nir_complet, $matches)) {
      return null;
    }

    if (empty ($birth_date)) {
      $birth_date = "000000";
    }

    $first_name = str_replace(" ", "", $first_name);

    if (strlen($first_name) > 10) {
      $first_name = mb_strimwidth($first_name, 0, 10);
    }
    else {
      $first_name = str_pad($first_name, 10);
    }

    $birth_date_length = strlen($birth_date);

    switch ($birth_date_length) {
      case 6:
        list($year, $month, $day) = str_split($birth_date, 2);
        break;
      case 8:
        list($day, $month, $year2, $year) = str_split($birth_date, 2);
        break;
      default:
        return null;
    }
    $birth_date = $year.$month.$day;

    if ($birth_date !== "000000" && $birth_date_length !== 6) {
      return null;
    }

    $seed = $first_name.$birth_date.$nir;

    $sha256 = hash("SHA256", $seed);
    $sha256_hex = substr($sha256, 0, 16);
    $insc = self::bchexdec($sha256_hex);

    if (strlen($insc) < 20) {
      $insc = str_pad($insc, 20, 0, STR_PAD_LEFT);
    }

    $insc_key = 97 - bcmod($insc, 97);
    $insc_key = str_pad($insc_key, 2, 0, STR_PAD_LEFT);

    return $insc.$insc_key;
  }

  /**
   * Transform the hexadecimal to decimal
   *
   * @param String $hex String
   *
   * @return int|string
   */
  static function bchexdec($hex) {
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
      $dec = bcadd($dec, bcmul(hexdec($hex[$i - 1]), bcpow('16', $len - $i)));
    }
    if (strpos($dec, ".") !== false) {
      $array = explode(".", $dec);
      $dec = $array[0];
    }
    return $dec;
  }

}