<?php

/**
 * Language string constants - Hungarian
 *
 * Clonefish form generator class
 * (c) phpformclass.com, Dots Amazing
 * All rights reserved.
 * 
 * @copyright 2010 Dots Amazing
 * @version   v2.2, 2010-10-21
 * @link      http://phpformclass.com
 * @package   clonefish
 *
 */

// element.inputfile.php
define('CF_STR_FILE_UPLOADS_NOT_ALLOWED', 'ezen a webszerveren a filefeltöltés nincs engedélyezve' );
define('CF_STR_IMAGE_DELETE', 'Kép törlése');

// form.php
define('CF_STR_FORM_ERRORS', 'Az adatok hiányosságai a következõk:');

// validation.date.php
define('CF_STR_DATE_FORMAT',  "A(z) '%s' mezõben az idõpont hibás! A következõ formátumú idõpont írható be: %s");
define('CF_STR_DATE_OVER_MINIMUM', "A megadható legkisebb dátum/idõ '%s' a(z) '%s' mezõben!" );
define('CF_STR_DATE_OVER_MAXIMUM', "A megadható legnagyobb dátum/idõ '%s' a(z) '%s' mezõben!" );
define('CF_STR_DATE_OVER_LESSFIELD',      "The date/time in '%s' must be less than '%s'!" );
define('CF_STR_DATE_OVER_LESSEQFIELD',    "The date/time in '%s' must be less or equal than '%s'!" );
define('CF_STR_DATE_OVER_GREATERFIELD',   "The date/time in '%s' must be greater than '%s'!" );
define('CF_STR_DATE_OVER_GREATEREQFIELD', "The date/time in '%s' must be greater or equal than '%s'!" );

// validation.file.php
define('CF_STR_FILE_REQUIRED',      "A(z) '%s' állomány kiválasztása kötelezõ");
define('CF_STR_FILE_TYPES_ALLOWED', "A(z) '%s' állománynak %s típusúnak kell lennie");
define('CF_STR_FILE_MINIMUM',       "A(z) '%s' állomány legalább %s byte méretû kell legyen");
define('CF_STR_FILE_MAXIMUM',       "A(z) '%s' állomány legfeljebb %s byte méretû lehet");
define('CF_STR_FILE_OR',            " vagy ");

// validation.number.php
define('CF_STR_NUMBER_MINIMUM',     "A(z) '%s' mezõbe írható legkisebb érték: %s");
define('CF_STR_NUMBER_MAXIMUM',     "A(z) '%s' mezõbe írható legnagyobb érték: %s");
define('CF_STR_NUMBER_NUMBERSONLY', "A(z) '%s' mezõbe csak szám írható");
define('CF_STR_NUMBER_INTEGERONLY', "A(z) '%s' mezõbe csak egész szám írható");
define('CF_STR_NUMBER_SCIENTIFICNOTATIONONLY', "A(z) '%s' mezõbe decimális, hexadecimális vagy tudományos jelölésű szám írható." );

// validation.string.php
define('CF_STR_STRING_MINIMUM',   "A(z) '%s' mezõnek legalább %s karakter hosszúnak kell lennie");
define('CF_STR_STRING_MAXIMUM',   "A(z) '%s' mezõ legfeljebb %s karakter hosszú lehet");
define('CF_STR_STRING_REGEXP',    "A(z) '%s' mezõ formátuma nem megfelelõ! (%s)");
define('CF_STR_STRING_NOT_EQUAL', "A(z) '%s' mezõbe ugyanazt kell írni, mint a(z) '%s' mezõbe!");
define('CF_STR_STRING_NOT_DIFFERENT', "A(z) '%s' és a(z) '%s' mezõ tartalma nem lehet egyenlõ!");

// validation.required.inputcheckbox.php
define('CF_STR_REQUIRED_CHECKBOX',      "A(z) '%s' jelölõdoboz bekapcsolása kötelezõ");

// validation.required.inputcheckboxdynamic.php
define('CF_STR_REQUIRED_MINIMUM_CHECKBOXES',      "Legalább %s darab '%s' jelölõdoboz kiválasztása kötelezõ");
define('CF_STR_REQUIRED_MAXIMUM_CHECKBOXES',      "Legfeljebb %s darab '%s' jelölõdobozt lehet kiválasztani");

// validation.required.inputradio.php
define('CF_STR_REQUIRED_RADIO',      "A(z) '%s' lehetõségek közül kötelezõ választani");

// validation.required.inputtext.php
define('CF_STR_REQUIRED_TEXT',      "A(z) '%s' mezõ kitöltése kötelezõ");

// validation.required.select.php
define('CF_STR_REQUIRED_SELECT',      "A(z) '%s' elemei közül kötelezõ választani");

// validation.custom.php
define('CF_STR_CUSTOM_VALIDATION_FAILS', "A(z) '%s' mezõ tartalma nem megfelelõ!");

// validation.database.php
define('CF_STR_DATABASE_INPUT_INVALID',  "A(z) '%s' mezõ tartalma nem megfelelõ!");

// validation.captcha.php, validation.recaptcha.php
define('CF_STR_CAPTCHA_ERROR',     "A karakterek a(z) '%s' mezõben nem egyeztek meg a képen olvasható karakterekkel");

// general form configuration/runtime errors

define('CF_ERR_MISSING_SLASHES_ADDED_PARAMETER', 'CLONEFISH: hiányzik a "slashes added" paraméter: %s');

define('CF_ERR_CONFIG_TYPE_MISSING',                      'CLONEFISH: "%s" input: hiányzik a "type" a konfigurációból' );
define('CF_ERR_CONFIG_TYPE_UNSUPPORTED',                  'CLONEFISH: "%s" input: "%s" mezõtípust az ûrlapgeneráló osztály nem támogatja, "%s" állomány nem található' );
define('CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY',           'CLONEFISH: "%s" input: a "validation" beállításnak tömbnek kell lennie, például: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>' );
define('CF_ERR_CONFIG_VALIDATION_NOT_AN_ARRAY_OF_ARRAYS', 'CLONEFISH: "%s" input: a "validation" tömbnek tömböket kell tartalmaznia, például: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>' );
define('CF_ERR_CONFIG_VALIDATION_TYPE_MISSING',           'CLONEFISH: "%s" input: a "type" beállítás hiányzik a "validation" tömb egyik elemébõl, például: <PRE>\'validation\' => Array( Array( \'type\' => \'required\' ) )</PRE>');
define('CF_ERR_VALUE_ARRAY_REQUIRED',                     'CLONEFISH: "%s" input: ha a "values" beállítás létezik, akkor tömböt kell tartalmaznia ehhez az inputhoz, például: <PRE>\'values\' => Array( 0 => \'nem\', 1 => \'igen\' )</PRE>');
define('CF_ERR_CONFIG_SINGLE_VALUE_REQUIRED',             'CLONEFISH: "%s" input: ha a "value" beállítás létezik, akkor nem tartalmazhat tömböt, csak egyszerû elemet, például: <PRE>\'value\'  => 142</PRE>');
define('CF_ERR_CONFIG_DATABASE_VALIDATION_SQL_MISSING',   'CLONEFISH: "%s" input (adatbázis alapú ellenõrzéskor): hiányzik az "sql" beállítás, amely az ellenõrzéshez szükséges' );
define('CF_ERR_CONFIG_DATABASE_VALIDATION_DB_MISSING',    'CLONEFISH: "%s" input (adatbázis alapú ellenõrzéskor): az adatbázis típusának beállítása (és/vagy az adatbázis objektum AdoDB, PearDB esetén) hiányzik a Clonefish konstruktor meghívásakor' );
define('CF_ERR_JSVALIDATOR_UNSUPPORTED',                  'CLONEFISH: "%s" input: nem támogatott JavaScript validálástípus: "%s"' );
define('CF_ERR_PHPVALIDATOR_UNSUPPORTED',                 'CLONEFISH: "%s" input: nem támogatott PHP validálástípus: "%s"' );
define('CF_ERR_REQUIRE_UNSUPPORTED',                      'CLONEFISH: "%s" input: az ûrlaposztály nem támogatja a következõ osztályt kötelezõségre: "%s"');
define('CF_ERR_FILE_VALIDATION_UNSUPPORTED',              'CLONEFISH: "%s" input (állományfeltöltõ inputmezõ): "%s" filetípusra ellenõrzést az ellenõrzõ osztály nem támogatja' );
define('CF_ERR_MISSING_DB_OBJECT',                        'CLONEFISH: "%s" input (dinamikus inputmezõ): az AdoDB adatbázisobjektuma nem elérhetõ (esetleg nem adták át a form objektumnak létrehozáskor)' );
define('CF_ERR_DATABASE_VALIDATION_DB_ERROR',             'CLONEFISH: "%s" input (adatbázis alapú ellenõrzéskor): adatbázishiba: "%s"' );
define('CF_ERR_DATABASE_VALIDATION_VALUE_OR_FIELD_MISSING','CLONEFISH: "%s" input (adatbázis alapú ellenõrzéskor): a "field" vagy a "value" beállítások egyike hiányzik, a másik mégis definiált - a két beállítás csak egyszerre használható' );
define('CF_ERR_DIRECTORY_INACCESSIBLE',                   'CLONEFISH: "%s" input (állomány kiválasztó): a(z) "%s" alkönyvtár nem létezik, vagy nem elérhetõ (vagy nem definiálták a "directory" tömbelemet)');
define('CF_ERR_DB_ERROR',                                 'CLONEFISH: "%s" input (dinamikus inputmezõ): adatbázishiba: "%s"' );
define('CF_ERR_TEMPLATE_MISSING_ELEMENT',                 'CLONEFISH: "%s" input (template): missing element: "%s"' );
define('CF_ERR_CONFIG_RECAPTCHA_LIBRARY_MISSING',         'CLONEFISH: hiányzik a reCAPTCHA fügvénytár, letölthető innen http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest');
define('CF_ERR_CONFIG_RECAPTCHA_APIKEY_MISSING',          'CLONEFISH: hiányzik a reCAPTCHA API kulcs');

// dbwrapper.php errors
define('CF_ERR_DBWRAPPER_DBTYPE_UNKNOWN', "CLONEFISH: Database connection type '%s' is unknown" );
define('CF_ERR_DBWRAPPER_NOT_MYSQL',  "CLONEFISH: a CloneFish objektumnak átadott adatbázisváltozó nem PHP resource típusú vagy nem MySQL link resource típusú" );
define('CF_ERR_DBWRAPPER_NOT_ADODB',  "CLONEFISH: a CloneFish objektumnak átadott adatbázisváltozó nem AdoDB connection objektum" );
define('CF_ERR_DBWRAPPER_NOT_PEARDB', "CLONEFISH: a CloneFish objektumnak átadott adatbázisváltozó nem PearDB connection objektum" );
define('CF_ERR_DBWRAPPER_NOT_MDB2',   "CLONEFISH: a CloneFish objektumnak átadott adatbázisváltozó nem MDB2 connection objektum" );
define('CF_ERR_DBWRAPPER_NOT_PDO',    "CLONEFISH: a CloneFish objektumnak átadott adatbázisváltozó nem PDO connection objektum" );

define('CF_STR_MONTH_01', 'január'    );
define('CF_STR_MONTH_02', 'február'   );
define('CF_STR_MONTH_03', 'március'   );
define('CF_STR_MONTH_04', 'április'   );
define('CF_STR_MONTH_05', 'május'     );
define('CF_STR_MONTH_06', 'június'    );
define('CF_STR_MONTH_07', 'július'    );
define('CF_STR_MONTH_08', 'augusztus' );
define('CF_STR_MONTH_09', 'szeptember');
define('CF_STR_MONTH_10', 'október'   );
define('CF_STR_MONTH_11', 'november'  );
define('CF_STR_MONTH_12', 'december'  );

?>