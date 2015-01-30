<?php
define('NUM_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x2c);
define('SMALL_BLOCK_DEPOT_BLOCK_POS', 0x3c);
define('ROOT_START_BLOCK_POS', 0x30);
define('BIG_BLOCK_SIZE', 0x200);
define('SMALL_BLOCK_SIZE', 0x40);
define('EXTENSION_BLOCK_POS', 0x44);
define('NUM_EXTENSION_BLOCK_POS', 0x48);
define('PROPERTY_STORAGE_BLOCK_SIZE', 0x80);
define('BIG_BLOCK_DEPOT_BLOCKS_POS', 0x4c);
define('SMALL_BLOCK_THRESHOLD', 0x1000);
// property storage offsets
define('SIZE_OF_NAME_POS', 0x40);
define('TYPE_POS', 0x42);
define('START_BLOCK_POS', 0x74);
define('SIZE_POS', 0x78);
define('IDENTIFIER_OLE', pack("CCCCCCCC",0xd0,0xcf,0x11,0xe0,0xa1,0xb1,0x1a,0xe1));

//echo 'ROOT_START_BLOCK_POS = '.ROOT_START_BLOCK_POS."\n";

//echo bin2hex($data[ROOT_START_BLOCK_POS])."\n";
//echo "a=";
//echo $data[ROOT_START_BLOCK_POS];
//function log

function GetInt4d($data, $pos)
{
  $value = ord($data[$pos]) | (ord($data[$pos+1])  << 8) | (ord($data[$pos+2]) << 16) | (ord($data[$pos+3]) << 24);
  if ($value>=4294967294)
  {
    $value=-2;
  }
  return $value;
}

?>