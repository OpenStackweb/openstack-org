<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class LanguagesSeedMigration
 */
final class LanguagesSeedMigration extends AbstractDBMigrationTask
{
    protected $title = "LanguagesSeedMigration";

    protected $description = "LanguagesSeedMigration";

    function doUp()
    {

        $sql = <<<SQL
-- Languages --
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(1, 'English', 'en');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(2, 'Afar', 'aa');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(3, 'Abkhazian', 'ab');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(4, 'Afrikaans', 'af');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(5, 'Amharic', 'am');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(6, 'Arabic', 'ar');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(7, 'Assamese', 'as');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(8, 'Aymara', 'ay');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(9, 'Azerbaijani', 'az');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(10, 'Bashkir', 'ba');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(11, 'Belarusian', 'be');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(12, 'Bulgarian', 'bg');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(13, 'Bihari', 'bh');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(14, 'Bislama', 'bi');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(15, 'Bengali/Bangla', 'bn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(16, 'Tibetan', 'bo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(17, 'Breton', 'br');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(18, 'Catalan', 'ca');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(19, 'Corsican', 'co');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(20, 'Czech', 'cs');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(21, 'Welsh', 'cy');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(22, 'Danish', 'da');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(23, 'German', 'de');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(24, 'Bhutani', 'dz');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(25, 'Greek', 'el');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(26, 'Esperanto', 'eo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(27, 'Spanish', 'es');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(28, 'Estonian', 'et');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(29, 'Basque', 'eu');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(30, 'Persian', 'fa');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(31, 'Finnish', 'fi');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(32, 'Fiji', 'fj');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(33, 'Faeroese', 'fo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(34, 'French', 'fr');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(35, 'Frisian', 'fy');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(36, 'Irish', 'ga');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(37, 'Scots/Gaelic', 'gd');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(38, 'Galician', 'gl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(39, 'Guarani', 'gn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(40, 'Gujarati', 'gu');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(41, 'Hausa', 'ha');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(42, 'Hindi', 'hi');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(43, 'Croatian', 'hr');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(44, 'Hungarian', 'hu');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(45, 'Armenian', 'hy');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(46, 'Interlingua', 'ia');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(47, 'Interlingue', 'ie');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(48, 'Inupiak', 'ik');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(49, 'Indonesian', 'in');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(50, 'Icelandic', 'is');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(51, 'Italian', 'it');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(52, 'Hebrew', 'iw');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(53, 'Japanese', 'ja');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(54, 'Yiddish', 'ji');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(55, 'Javanese', 'jw');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(56, 'Georgian', 'ka');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(57, 'Kazakh', 'kk');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(58, 'Greenlandic', 'kl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(59, 'Cambodian', 'km');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(60, 'Kannada', 'kn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(61, 'Korean', 'ko');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(62, 'Kashmiri', 'ks');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(63, 'Kurdish', 'ku');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(64, 'Kirghiz', 'ky');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(65, 'Latin', 'la');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(66, 'Lingala', 'ln');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(67, 'Laothian', 'lo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(68, 'Lithuanian', 'lt');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(69, 'Latvian/Lettish', 'lv');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(70, 'Malagasy', 'mg');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(71, 'Maori', 'mi');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(72, 'Macedonian', 'mk');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(73, 'Malayalam', 'ml');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(74, 'Mongolian', 'mn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(75, 'Moldavian', 'mo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(76, 'Marathi', 'mr');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(77, 'Malay', 'ms');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(78, 'Maltese', 'mt');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(79, 'Burmese', 'my');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(80, 'Nauru', 'na');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(81, 'Nepali', 'ne');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(82, 'Dutch', 'nl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(83, 'Norwegian', 'no');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(84, 'Occitan', 'oc');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(85, '(Afan)/Oromoor/Oriya', 'om');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(86, 'Punjabi', 'pa');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(87, 'Polish', 'pl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(88, 'Pashto/Pushto', 'ps');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(89, 'Portuguese', 'pt');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(90, 'Quechua', 'qu');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(91, 'Rhaeto-Romance', 'rm');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(92, 'Kirundi', 'rn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(93, 'Romanian', 'ro');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(94, 'Russian', 'ru');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(95, 'Kinyarwanda', 'rw');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(96, 'Sanskrit', 'sa');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(97, 'Sindhi', 'sd');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(98, 'Sangro', 'sg');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(99, 'Serbo-Croatian', 'sh');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(100, 'Singhalese', 'si');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(101, 'Slovak', 'sk');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(102, 'Slovenian', 'sl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(103, 'Samoan', 'sm');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(104, 'Shona', 'sn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(105, 'Somali', 'so');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(106, 'Albanian', 'sq');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(107, 'Serbian', 'sr');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(108, 'Siswati', 'ss');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(109, 'Sesotho', 'st');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(110, 'Sundanese', 'su');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(111, 'Swedish', 'sv');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(112, 'Swahili', 'sw');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(113, 'Tamil', 'ta');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(114, 'Telugu', 'te');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(115, 'Tajik', 'tg');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(116, 'Thai', 'th');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(117, 'Tigrinya', 'ti');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(118, 'Turkmen', 'tk');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(119, 'Tagalog', 'tl');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(120, 'Setswana', 'tn');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(121, 'Tonga', 'to');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(122, 'Turkish', 'tr');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(123, 'Tsonga', 'ts');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(124, 'Tatar', 'tt');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(125, 'Twi', 'tw');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(126, 'Ukrainian', 'uk');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(127, 'Urdu', 'ur');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(128, 'Uzbek', 'uz');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(129, 'Vietnamese', 'vi');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(130, 'Volapuk', 'vo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(131, 'Wolof', 'wo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(132, 'Xhosa', 'xh');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(133, 'Yoruba', 'yo');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(134, 'Chinese', 'zh');
INSERT INTO `Language` (ID, `Name`,IsoCode_639_1) VALUES(135, 'Zulu', 'zu');
SQL;

        foreach(explode(';', $sql) as $statement)
            if(!empty($statement)) DB::query($statement);
    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}