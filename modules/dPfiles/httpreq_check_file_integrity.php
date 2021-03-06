<?php
/**
 * $Id: httpreq_check_file_integrity.php 20498 2013-09-29 19:08:17Z phenxdesign $
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision: 20498 $
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

CApp::setTimeLimit(300);

$show = CValue::get("show", 50);

// Search files without documents
$files = glob(CFile::$directory . "/*/*/*/*");

$filesCount = 0;
$filesWithoutDocCount = 0;
$filesWithoutDocTruncated = array();
$filesWithBadDocCount = 0;
$filesWithBadDocTruncated = array();
foreach ($files as $filePath) {
  $filesCount++;
  $fileName = basename($filePath);
  $fileObjectId = basename(dirname($filePath));
  $fileObjectClass = basename(dirname(dirname(dirname($filePath))));

  $where = array (
    "file_real_filename" => "= '$fileName'",
  );
  
  $doc = new CFile;
  $doc->loadObject($where);

  if (!$doc->file_id) {
    $filesWithoutDocCount++;
    if (count($filesWithoutDocTruncated) < $show) {
      $filesWithoutDocTruncated[] = array(
        "fileName" => $fileName,
        "fileObjectId" => $fileObjectId,
        "fileObjectClass" => $fileObjectClass,
        "filePath" => $filePath,
      );
    }
  }

  if ($doc->file_id) {
    if ($doc->object_id != $fileObjectId or $doc->object_class != $fileObjectClass) {
      $filesWithBadDocCount++;
      if (count($filesWithBadDocTruncated) < $show) {
        $filesWithBadDocTruncated[] = array(
          "fileName" => $fileName,
          "fileObjectId" => $fileObjectId,
          "fileObjectClass" => $fileObjectClass,
          "filePath" => $filePath,
        );
      }
    }
  }
}

// Search document without files
$stepSize = 1000;
$step = 0;

$docsWithoutFileTruncated = array();
$docsCount = 0;
$docsWithoutFileCount = 0;
do {
  $offset = $step * $stepSize;  
  
  $limit = "$offset, $stepSize";
  $docs = new CFile();

  /** @var CFile[] $docs */
  $docs = $docs->loadList(null, null, $limit);
  foreach ($docs as $keyDoc => $valDoc) {
    $doc =& $docs[$keyDoc];
    $docsCount++;
    if (!is_file($doc->_file_path)) {
      $docsWithoutFileCount++;
      if (count($docsWithoutFileTruncated) < $show) {
        $docsWithoutFileTruncated[$keyDoc] =& $doc;
      }
    }
  }
  $step++;
} while (count($docs));

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("show", $show);
$smarty->assign("docsWithoutFileTruncated", $docsWithoutFileTruncated);
$smarty->assign("docsWithoutFileCount", $docsWithoutFileCount);
$smarty->assign("docsCount", $docsCount);
$smarty->assign("filesWithoutDocTruncated", $filesWithoutDocTruncated);
$smarty->assign("filesWithoutDocCount", $filesWithoutDocCount);
$smarty->assign("filesWithBadDocTruncated", $filesWithBadDocTruncated);
$smarty->assign("filesWithBadDocCount", $filesWithBadDocCount);
$smarty->assign("filesCount", $filesCount);

$smarty->display("inc_check_file_integrity.tpl");

