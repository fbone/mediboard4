<?php

/**
 * Exchange H'XML
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 23935 $
 */

/**
 * Class CEchangeHprim
 */
class CEchangeHprim extends CEchangeXML {
  static $messages = array(
    "patients" => "CHPrimXMLEventPatient",
    "pmsi"     => "CHPrimXMLEventServeurActivitePmsi"
  );

  // DB Table key
  public $echange_hprim_id;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'echange_hprim';
    $spec->key   = 'echange_hprim_id';
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["receiver_id"]   = "ref class|CDestinataireHprim";
    $props["initiateur_id"] = "ref class|CEchangeHprim";
    $props["object_class"]  = "enum list|CPatient|CSejour|COperation|CAffectation|CConsultation show|0";

    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['notifications'] = "CEchangeHprim initiateur_id";

    return $backProps;
  }

  /**
   * @see parent::loadRefsBack()
   */
  function loadRefsBack() {
    parent::loadRefsBack();

    $this->loadRefNotifications();
  }

  /**
   * Load notifications
   *
   * @return void
   */
  function loadRefNotifications(){
    $this->_ref_notifications = $this->loadBackRefs("notifications");
  }

  /**
   * Get errors
   *
   * @return array|void
   */
  function getErrors() {
    if ($this->_message !== null) {
      $evt = null;
      $this->type == "patients" ?
        $evt = new CHPrimXMLEventPatient() : null;
      $this->type == "pmsi" ?
        $evt = new CHPrimXMLEventServeurActivitePmsi() : null;

      $domGetEvenement = $evt->getHPrimXMLEvenements($this->_message);
      $domGetEvenement->formatOutput = true;

      $validate = $domGetEvenement->schemaValidate(null, true, false);
      if (!is_bool($validate)) {
        $errors = explode("\n", utf8_decode($validate));
        $this->_doc_errors_msg = array_filter($errors);
      }

      $this->_message = utf8_encode($domGetEvenement->saveXML());
    }

    if ($this->_acquittement !== null) {
      $domGetAcquittement = null;
      $this->type == "patients" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsPatients() : null;
      $this->type == "pmsi" ?
        $domGetAcquittement = new CHPrimXMLAcquittementsServeurActivitePmsi::$evenements[$this->sous_type] : null;

      if (!$domGetAcquittement) {
        return;
      }

      $domGetAcquittement->loadXML($this->_acquittement);
      $domGetAcquittement->formatOutput = true;

      $validate = $domGetAcquittement->schemaValidate(null, true, false);
      if (!is_bool($validate)) {
        $errors = explode("\n", utf8_decode($validate));
        $this->_doc_errors_ack = array_filter($errors);
      }

      $this->_acquittement = utf8_encode($domGetAcquittement->saveXML());
    }
  }

  /**
   * Get observations
   *
   * @param bool $display_errors Display errors
   *
   * @return array
   */
  function getObservations($display_errors = false) {
    if (!$this->_acquittement) {
      return null;
    }

    if ($this->type == "patients") {
      $domGetAcquittement = new CHPrimXMLAcquittementsPatients();
      $domGetAcquittement->loadXML($this->_acquittement);
      $doc_valid = $domGetAcquittement->schemaValidate(null, false, false);
      if ($doc_valid) {
        return $this->_observations = $domGetAcquittement->getAcquittementObservationPatients();
      }
    }
    /* @todo a remplir ... */
    if ($this->type == "pmsi") {
      return $this->_observations = array();
    }

    return null;
  }

  /**
   * @see parent::loadView()
   */
  function loadView() {
    parent::loadView();

    $this->getObservations();
  }

  /**
   * Set object class & id
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function setObjectClassIdPermanent(CMbObject $mbObject) {
    $this->object_class = $mbObject->_class;

    if ($mbObject instanceof CPatient && $mbObject->_IPP) {
      $this->id_permanent = $mbObject->_IPP;
    }
    if ($mbObject instanceof CSejour && $mbObject->_NDA) {
      $this->id_permanent = $mbObject->_NDA;
    }
  }

  /**
   * @see parent::handle()
   */
  function handle() {
    return COperatorHprimXML::event($this);
  }

  /**
   * @see parent::getFamily()
   */
  function getFamily() {
    return self::$messages;
  }

  /**
   * Populate exchange
   *
   * @param CExchangeDataFormat $data_format Data format
   * @param CHPrimXMLEvenements $dom_evt     Event H'XML
   *
   * @return string
   */
  function populateEchange(CExchangeDataFormat $data_format, CHPrimXMLEvenements $dom_evt) {
    $this->date_production = CMbDT::dateTime();
    $this->group_id        = $data_format->group_id;
    $this->sender_id       = $data_format->sender_id;
    $this->sender_class    = $data_format->sender_class;
    $this->type            = $dom_evt->type;
    $this->sous_type       = $dom_evt->sous_type ? $dom_evt->sous_type : "inconnu";
    $this->_message        = $data_format->_message;;
  }

  /**
   * Populate error exchange
   *
   * @param string $msgAcq     Acknowledgment
   * @param bool   $doc_valid  Document is valid ?
   * @param string $type_error Error type
   *
   * @return string|void
   */
  function populateErrorEchange($msgAcq, $doc_valid, $type_error) {
    $this->_acquittement       = $msgAcq;
    $this->statut_acquittement = $type_error;
    $this->message_valide      = 0;
    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->date_echange        = CMbDT::dateTime();
    $this->store();
  }

  /**
   * Generate acknowledgment 'OK', 'AVT'
   *
   * @param CHPrimXMLAcquittements $dom_acq       Acknowledgment
   * @param array                  $codes         Mediboard errors codes
   * @param array                  $avertissement Warning
   * @param null                   $commentaires  Comments
   * @param CMbObject              $mbObject      Object
   * @param array                  $data          Objects
   *
   * @return string
   */
  function setAck(
      CHPrimXMLAcquittements $dom_acq, $codes, $avertissement = null, $commentaires = null, CMbObject $mbObject = null, $data = array()
  ) {
    $commentaire = $avertissement ? $avertissement : $commentaires;
    $statut      = $avertissement ? $dom_acq->_codes_erreurs["avt"] : $dom_acq->_codes_erreurs["ok"];

    $msgAcq    = $dom_acq->generateAcquittements($statut, $codes, $commentaire, $mbObject, $data);
    $doc_valid = $dom_acq->schemaValidate(null, false, $this->_ref_receiver ? $this->_ref_receiver->display_errors : true);

    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->statut_acquittement = $statut;

    if ($mbObject) {
      $this->setObjectIdClass($mbObject);
    }
    $this->_acquittement = $msgAcq;
    $this->date_echange  = CMbDT::dateTime();
    $this->store();

    return $msgAcq;
  }

  /**
   * Generate acknowledgment 'ERR'
   *
   * @param CHPrimXMLAcquittements $dom_acq      Acknowledgment
   * @param array                  $code_erreur  Mediboard errors codes
   * @param null                   $commentaires Comments
   * @param CMbObject              $mbObject     Object
   * @param array                  $data         Objects
   *
   * @return string
   */
  function setAckError(CHPrimXMLAcquittements $dom_acq, $code_erreur, $commentaires = null, CMbObject $mbObject = null, $data = null) {
    $statut = $dom_acq->_codes_erreurs["err"];

    $msgAcq    = $dom_acq->generateAcquittements($dom_acq->_codes_erreurs["err"], $code_erreur, $commentaires, $mbObject, $data);
    $doc_valid = $dom_acq->schemaValidate(null, false, $this->_ref_receiver ? $this->_ref_receiver->display_errors : true);

    $this->acquittement_valide = $doc_valid ? 1 : 0;
    $this->statut_acquittement = $statut;

    if ($mbObject) {
      $this->setObjectIdClass($mbObject);
    }
    $this->_acquittement = $msgAcq;
    $this->date_echange = CMbDT::dateTime();
    $this->store();

    return $msgAcq;
  }

  /**
   * Get H'XML config for one actor
   *
   * @param string $actor_guid Actor GUID
   *
   * @return CHprimXMLConfig
   */
  function getConfigs($actor_guid) {
    list($sender_class, $sender_id) = explode("-", $actor_guid);

    $hprimxml_config = new CHprimXMLConfig();
    $hprimxml_config->sender_class = $sender_class;
    $hprimxml_config->sender_id    = $sender_id;
    $hprimxml_config->loadMatchingObject();

    return $this->_configs_format = $hprimxml_config;
  }
}
