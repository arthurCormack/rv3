<?php



function decryptSFData($data) {
  $someExportedData = var_export($data, true);
  $someData = new stdClass;
  $someData->test = "oranges";
  $someData->data = $data['data'];
  return ($someData);
  //
}