<?php
/**
 * Ending installation step
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id: 07_finish.php 23195 2014-05-19 10:22:17Z phenxdesign $ 
 * @link       http://www.mediboard.org
 */

require_once "includes/checkconfig.php";
require_once "includes/checkstructure.php";
require_once "includes/checkauth.php";

showHeader(); 

?>

<script type="text/javascript">

function checkForm(oForm) {
  if (oForm.password1.value != oForm.password2.value) {
    alert("Les deux mots de passe ne sont pas identiques");
    oForm.reset();
    oForm.password1.focus();
    return false;
  }
  
  if (oForm.password1.value.length < 8) {
    alert("Le mot de passe est trop court (moins de 8 caract�res)");
    oForm.reset();
    oForm.password1.focus();
    return false;
  }
  
  return true;
}

</script>
  
<h2>Finalisation de l'installation</h2>

<h3>Changement du mot de passe administrateur</h3>

<p>
  L'assistant d'installation du framework g�n�ral est fonctionnel mais n�cessite d'�tre
  s�curis�. Il est obligatoire de fournir un mot de passe administrateur s�r.
</p>

<div class="small-warning">
  L'administrateur a pour login 'admin'. Attention, toute la s�curit� du syst�me se r�sume � la s�curit� de ce mot de passe.
  Il est recommand� d'utiliser une s�quence de plus de 8 caract�res compos�e de lettres,
  minuscules et majuscules, de chiffres et d'autres symboles comme @$%^, etc.
</div>

<form name="changePassword" action="07_finish.php" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="changePassword" value="true" />
<table class="form">

  <tr>
    <th class="category" colspan="2">Changer le mot de passe administrateur</th>
  </tr>

  <tr>
    <th><label for="password1" title="Saisir un mot de passe fiable">Saisir le mot de passe :</label></th>
    <td><input type="password" size="20" id="password1" name="password1" value="" autofocus /></td>
  </tr>

  <tr>
    <th><label for="password2" title="Re-saisir le mot de passe pour v�rification">Re-saisir le mot de passe :</label></th>
    <td><input type="password" size="20" id="password2" name="password2" value="" /></td>
  </tr>


  <tr>
    <td class="button" colspan="2">
      <button class="modify" type="submit">Valider le mot de passe</button>
    </td>
  </tr>

</table>

</form>

<?php
if (@$_POST["changePassword"] && $_POST["password1"] === $_POST["password2"]) {
  $password = $_POST["password1"];
  
  // TODO salt will never be ready for now, fix it
  $db->query("UPDATE $table SET $passCol = ? WHERE $userCol = 'admin'", array(md5($password)));
?>

<table class="tbl">

<tr>
  <th>Action</th>
  <th>Statut</th>
</tr>

<tr>
  <td>Modification du mot de passe 'admin'</td>
  <td>
    <?php if (!$res instanceof PEAR_Error) { ?>
    <div class="info">Mot de passe modifi�</div>
    <?php } else { ?>
    <div class="error">
      Erreur lors du changement de mot de passe
      <br />
      <?php echo $res->getMessage(); ?>
    </div>
    <?php } ?>
  </td>
</tr>

</table>

<?php } ?>

<h3>Installer et configurer les modules</h3>

<?php
if (@$_POST["changePassword"] && $password != "admin") {
?>
<div class="small-success">
  F�licitations !
  <br />Le framework de Mediboard est maintenant op�rationnel.
</div>
<?php } ?>

<p>
  A cette �tape le framework g�n�ral de Mediboard est fonctionnel. Il est maintenant 
  n�cessaire d'installer et param�trer un par un les modules que vous souhaitez utiliser.
</p>

<div class="small-info">
  A l'heure actuelle, les couplages inter-modules sont encore assez importants, 
  c'est pourquoi il est recommand� sinon obligatoire de tous les installer, quitte � les
  d�sactiver ou les masquer du menu principal (<em>cf.</em> Administration des modules).
</div>

<p>  
  A partir de maintenant, il est n�cessaire de s'authentifier aupr�s du syst�me en tant
  qu'administatreur pour pouvoir configurer les modules.
</p>

<div class="navigation">
  <a class="button tick" href="../?m=system&amp;a=domodsql&amp;cmd=upgrade-core">
    Me rendre � la page d'administration des modules
  </a>
</div>

<?php showFooter(); ?>