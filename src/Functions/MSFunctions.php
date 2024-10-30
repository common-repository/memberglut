<?php
// 


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


use MemberGlut\Core\Base;

use MemberGlut\Core\Membership\Models\Plan\PlanFactory;
use MemberGlut\Core\Membership\Models\Plan\PlanEntity;
use MemberGlut\Core\Membership\Models\Subscription\SubscriptionEntity;



/**
 * @param $plan_id
 *
 * @return PlanEntity
 */
function mglut_get_plan($plan_id)
{
    return PlanFactory::fromId($plan_id);
}


/**
 * Check if website has an active membership plan.
 *
 * @return bool
 */
function mglut_is_any_active_plan()
{
    global $wpdb;

}


function mglut_get_currency()
{
    return apply_filters('mglut_currency', mglut_settings_by_key('payment_currency', 'USD', true));
}

/**
 * Get full list of currency codes.
 *
 * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
 *
 * @return array
 */
function mglut_get_currencies()
{
    static $currencies;

    if ( ! isset($currencies)) {
        $currencies = array_unique(
            apply_filters(
                'mglut_currencies',
                [
                    'USD' => __('United States (US) dollar', 'memberglut'),
                    'EUR' => __('Euro', 'memberglut'),
                    'GBP' => __('Pound sterling', 'memberglut'),
                    'AED' => __('United Arab Emirates dirham', 'memberglut'),
                    'AFN' => __('Afghan afghani', 'memberglut'),
                    'ALL' => __('Albanian lek', 'memberglut'),
                    'AMD' => __('Armenian dram', 'memberglut'),
                    'ANG' => __('Netherlands Antillean guilder', 'memberglut'),
                    'AOA' => __('Angolan kwanza', 'memberglut'),
                    'ARS' => __('Argentine peso', 'memberglut'),
                    'AUD' => __('Australian dollar', 'memberglut'),
                    'AWG' => __('Aruban florin', 'memberglut'),
                    'AZN' => __('Azerbaijani manat', 'memberglut'),
                    'BAM' => __('Bosnia and Herzegovina convertible mark', 'memberglut'),
                    'BBD' => __('Barbadian dollar', 'memberglut'),
                    'BDT' => __('Bangladeshi taka', 'memberglut'),
                    'BGN' => __('Bulgarian lev', 'memberglut'),
                    'BHD' => __('Bahraini dinar', 'memberglut'),
                    'BIF' => __('Burundian franc', 'memberglut'),
                    'BMD' => __('Bermudian dollar', 'memberglut'),
                    'BND' => __('Brunei dollar', 'memberglut'),
                    'BOB' => __('Bolivian boliviano', 'memberglut'),
                    'BRL' => __('Brazilian real', 'memberglut'),
                    'BSD' => __('Bahamian dollar', 'memberglut'),
                    'BTC' => __('Bitcoin', 'memberglut'),
                    'BTN' => __('Bhutanese ngultrum', 'memberglut'),
                    'BWP' => __('Botswana pula', 'memberglut'),
                    'BYR' => __('Belarusian ruble (old)', 'memberglut'),
                    'BYN' => __('Belarusian ruble', 'memberglut'),
                    'BZD' => __('Belize dollar', 'memberglut'),
                    'CAD' => __('Canadian dollar', 'memberglut'),
                    'CDF' => __('Congolese franc', 'memberglut'),
                    'CHF' => __('Swiss franc', 'memberglut'),
                    'CLP' => __('Chilean peso', 'memberglut'),
                    'CNY' => __('Chinese yuan', 'memberglut'),
                    'COP' => __('Colombian peso', 'memberglut'),
                    'CRC' => __('Costa Rican col&oacute;n', 'memberglut'),
                    'CUC' => __('Cuban convertible peso', 'memberglut'),
                    'CUP' => __('Cuban peso', 'memberglut'),
                    'CVE' => __('Cape Verdean escudo', 'memberglut'),
                    'CZK' => __('Czech koruna', 'memberglut'),
                    'DJF' => __('Djiboutian franc', 'memberglut'),
                    'DKK' => __('Danish krone', 'memberglut'),
                    'DOP' => __('Dominican peso', 'memberglut'),
                    'DZD' => __('Algerian dinar', 'memberglut'),
                    'EGP' => __('Egyptian pound', 'memberglut'),
                    'ERN' => __('Eritrean nakfa', 'memberglut'),
                    'ETB' => __('Ethiopian birr', 'memberglut'),
                    'FJD' => __('Fijian dollar', 'memberglut'),
                    'FKP' => __('Falkland Islands pound', 'memberglut'),
                    'GEL' => __('Georgian lari', 'memberglut'),
                    'GGP' => __('Guernsey pound', 'memberglut'),
                    'GHS' => __('Ghana cedi', 'memberglut'),
                    'GIP' => __('Gibraltar pound', 'memberglut'),
                    'GMD' => __('Gambian dalasi', 'memberglut'),
                    'GNF' => __('Guinean franc', 'memberglut'),
                    'GTQ' => __('Guatemalan quetzal', 'memberglut'),
                    'GYD' => __('Guyanese dollar', 'memberglut'),
                    'HKD' => __('Hong Kong dollar', 'memberglut'),
                    'HNL' => __('Honduran lempira', 'memberglut'),
                    'HRK' => __('Croatian kuna', 'memberglut'),
                    'HTG' => __('Haitian gourde', 'memberglut'),
                    'HUF' => __('Hungarian forint', 'memberglut'),
                    'IDR' => __('Indonesian rupiah', 'memberglut'),
                    'ILS' => __('Israeli new shekel', 'memberglut'),
                    'IMP' => __('Manx pound', 'memberglut'),
                    'INR' => __('Indian rupee', 'memberglut'),
                    'IQD' => __('Iraqi dinar', 'memberglut'),
                    'IRR' => __('Iranian rial', 'memberglut'),
                    'IRT' => __('Iranian toman', 'memberglut'),
                    'ISK' => __('Icelandic kr&oacute;na', 'memberglut'),
                    'JEP' => __('Jersey pound', 'memberglut'),
                    'JMD' => __('Jamaican dollar', 'memberglut'),
                    'JOD' => __('Jordanian dinar', 'memberglut'),
                    'JPY' => __('Japanese yen', 'memberglut'),
                    'KES' => __('Kenyan shilling', 'memberglut'),
                    'KGS' => __('Kyrgyzstani som', 'memberglut'),
                    'KHR' => __('Cambodian riel', 'memberglut'),
                    'KMF' => __('Comorian franc', 'memberglut'),
                    'KPW' => __('North Korean won', 'memberglut'),
                    'KRW' => __('South Korean won', 'memberglut'),
                    'KWD' => __('Kuwaiti dinar', 'memberglut'),
                    'KYD' => __('Cayman Islands dollar', 'memberglut'),
                    'KZT' => __('Kazakhstani tenge', 'memberglut'),
                    'LAK' => __('Lao kip', 'memberglut'),
                    'LBP' => __('Lebanese pound', 'memberglut'),
                    'LKR' => __('Sri Lankan rupee', 'memberglut'),
                    'LRD' => __('Liberian dollar', 'memberglut'),
                    'LSL' => __('Lesotho loti', 'memberglut'),
                    'LYD' => __('Libyan dinar', 'memberglut'),
                    'MAD' => __('Moroccan dirham', 'memberglut'),
                    'MDL' => __('Moldovan leu', 'memberglut'),
                    'MGA' => __('Malagasy ariary', 'memberglut'),
                    'MKD' => __('Macedonian denar', 'memberglut'),
                    'MMK' => __('Burmese kyat', 'memberglut'),
                    'MNT' => __('Mongolian t&ouml;gr&ouml;g', 'memberglut'),
                    'MOP' => __('Macanese pataca', 'memberglut'),
                    'MRU' => __('Mauritanian ouguiya', 'memberglut'),
                    'MUR' => __('Mauritian rupee', 'memberglut'),
                    'MVR' => __('Maldivian rufiyaa', 'memberglut'),
                    'MWK' => __('Malawian kwacha', 'memberglut'),
                    'MXN' => __('Mexican peso', 'memberglut'),
                    'MYR' => __('Malaysian ringgit', 'memberglut'),
                    'MZN' => __('Mozambican metical', 'memberglut'),
                    'NAD' => __('Namibian dollar', 'memberglut'),
                    'NGN' => __('Nigerian naira', 'memberglut'),
                    'NIO' => __('Nicaraguan c&oacute;rdoba', 'memberglut'),
                    'NOK' => __('Norwegian krone', 'memberglut'),
                    'NPR' => __('Nepalese rupee', 'memberglut'),
                    'NZD' => __('New Zealand dollar', 'memberglut'),
                    'OMR' => __('Omani rial', 'memberglut'),
                    'PAB' => __('Panamanian balboa', 'memberglut'),
                    'PEN' => __('Sol', 'memberglut'),
                    'PGK' => __('Papua New Guinean kina', 'memberglut'),
                    'PHP' => __('Philippine peso', 'memberglut'),
                    'PKR' => __('Pakistani rupee', 'memberglut'),
                    'PLN' => __('Polish z&#x142;oty', 'memberglut'),
                    'PRB' => __('Transnistrian ruble', 'memberglut'),
                    'PYG' => __('Paraguayan guaran&iacute;', 'memberglut'),
                    'QAR' => __('Qatari riyal', 'memberglut'),
                    'RON' => __('Romanian leu', 'memberglut'),
                    'RSD' => __('Serbian dinar', 'memberglut'),
                    'RUB' => __('Russian ruble', 'memberglut'),
                    'RWF' => __('Rwandan franc', 'memberglut'),
                    'SAR' => __('Saudi riyal', 'memberglut'),
                    'SBD' => __('Solomon Islands dollar', 'memberglut'),
                    'SCR' => __('Seychellois rupee', 'memberglut'),
                    'SDG' => __('Sudanese pound', 'memberglut'),
                    'SEK' => __('Swedish krona', 'memberglut'),
                    'SGD' => __('Singapore dollar', 'memberglut'),
                    'SHP' => __('Saint Helena pound', 'memberglut'),
                    'SLL' => __('Sierra Leonean leone', 'memberglut'),
                    'SOS' => __('Somali shilling', 'memberglut'),
                    'SRD' => __('Surinamese dollar', 'memberglut'),
                    'SSP' => __('South Sudanese pound', 'memberglut'),
                    'STN' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'memberglut'),
                    'SYP' => __('Syrian pound', 'memberglut'),
                    'SZL' => __('Swazi lilangeni', 'memberglut'),
                    'THB' => __('Thai baht', 'memberglut'),
                    'TJS' => __('Tajikistani somoni', 'memberglut'),
                    'TMT' => __('Turkmenistan manat', 'memberglut'),
                    'TND' => __('Tunisian dinar', 'memberglut'),
                    'TOP' => __('Tongan pa&#x2bb;anga', 'memberglut'),
                    'TRY' => __('Turkish lira', 'memberglut'),
                    'TTD' => __('Trinidad and Tobago dollar', 'memberglut'),
                    'TWD' => __('New Taiwan dollar', 'memberglut'),
                    'TZS' => __('Tanzanian shilling', 'memberglut'),
                    'UAH' => __('Ukrainian hryvnia', 'memberglut'),
                    'UGX' => __('Ugandan shilling', 'memberglut'),
                    'UYU' => __('Uruguayan peso', 'memberglut'),
                    'UZS' => __('Uzbekistani som', 'memberglut'),
                    'VEF' => __('Venezuelan bol&iacute;var', 'memberglut'),
                    'VES' => __('Bol&iacute;var soberano', 'memberglut'),
                    'VND' => __('Vietnamese &#x111;&#x1ed3;ng', 'memberglut'),
                    'VUV' => __('Vanuatu vatu', 'memberglut'),
                    'WST' => __('Samoan t&#x101;l&#x101;', 'memberglut'),
                    'XAF' => __('Central African CFA franc', 'memberglut'),
                    'XCD' => __('East Caribbean dollar', 'memberglut'),
                    'XOF' => __('West African CFA franc', 'memberglut'),
                    'XPF' => __('CFP franc', 'memberglut'),
                    'YER' => __('Yemeni rial', 'memberglut'),
                    'ZAR' => __('South African rand', 'memberglut'),
                    'ZMW' => __('Zambian kwacha', 'memberglut'),
                ]
            )
        );
    }

    return array_map('ucwords', $currencies);
}

/**
 * Get all available Currency symbols.
 *
 * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
 *
 * @return array
 */
function mglut_get_currency_symbols()
{
    $symbols = apply_filters(
        'mglut_currency_symbols',
        array(
            'AED' => '&#x62f;.&#x625;',
            'AFN' => '&#x60b;',
            'ALL' => 'L',
            'AMD' => 'AMD',
            'ANG' => '&fnof;',
            'AOA' => 'Kz',
            'ARS' => '&#36;',
            'AUD' => '&#36;',
            'AWG' => 'Afl.',
            'AZN' => 'AZN',
            'BAM' => 'KM',
            'BBD' => '&#36;',
            'BDT' => '&#2547;&nbsp;',
            'BGN' => '&#1083;&#1074;.',
            'BHD' => '.&#x62f;.&#x628;',
            'BIF' => 'Fr',
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => 'Bs.',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTC' => '&#3647;',
            'BTN' => 'Nu.',
            'BWP' => 'P',
            'BYR' => 'Br',
            'BYN' => 'Br',
            'BZD' => '&#36;',
            'CAD' => '&#36;',
            'CDF' => 'Fr',
            'CHF' => '&#67;&#72;&#70;',
            'CLP' => '&#36;',
            'CNY' => '&yen;',
            'COP' => '&#36;',
            'CRC' => '&#x20a1;',
            'CUC' => '&#36;',
            'CUP' => '&#36;',
            'CVE' => '&#36;',
            'CZK' => '&#75;&#269;',
            'DJF' => 'Fr',
            'DKK' => 'DKK',
            'DOP' => 'RD&#36;',
            'DZD' => '&#x62f;.&#x62c;',
            'EGP' => 'EGP',
            'ERN' => 'Nfk',
            'ETB' => 'Br',
            'EUR' => '&euro;',
            'FJD' => '&#36;',
            'FKP' => '&pound;',
            'GBP' => '&pound;',
            'GEL' => '&#x20be;',
            'GGP' => '&pound;',
            'GHS' => '&#x20b5;',
            'GIP' => '&pound;',
            'GMD' => 'D',
            'GNF' => 'Fr',
            'GTQ' => 'Q',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => 'L',
            'HRK' => 'kn',
            'HTG' => 'G',
            'HUF' => '&#70;&#116;',
            'IDR' => 'Rp',
            'ILS' => '&#8362;',
            'IMP' => '&pound;',
            'INR' => '&#8377;',
            'IQD' => '&#x62f;.&#x639;',
            'IRR' => '&#xfdfc;',
            'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
            'ISK' => 'kr.',
            'JEP' => '&pound;',
            'JMD' => '&#36;',
            'JOD' => '&#x62f;.&#x627;',
            'JPY' => '&yen;',
            'KES' => 'KSh',
            'KGS' => '&#x441;&#x43e;&#x43c;',
            'KHR' => '&#x17db;',
            'KMF' => 'Fr',
            'KPW' => '&#x20a9;',
            'KRW' => '&#8361;',
            'KWD' => '&#x62f;.&#x643;',
            'KYD' => '&#36;',
            'KZT' => '&#8376;',
            'LAK' => '&#8365;',
            'LBP' => '&#x644;.&#x644;',
            'LKR' => '&#xdbb;&#xdd4;',
            'LRD' => '&#36;',
            'LSL' => 'L',
            'LYD' => '&#x644;.&#x62f;',
            'MAD' => '&#x62f;.&#x645;.',
            'MDL' => 'MDL',
            'MGA' => 'Ar',
            'MKD' => '&#x434;&#x435;&#x43d;',
            'MMK' => 'Ks',
            'MNT' => '&#x20ae;',
            'MOP' => 'P',
            'MRU' => 'UM',
            'MUR' => '&#x20a8;',
            'MVR' => '.&#x783;',
            'MWK' => 'MK',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => 'MT',
            'NAD' => 'N&#36;',
            'NGN' => '&#8358;',
            'NIO' => 'C&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#x631;.&#x639;.',
            'PAB' => 'B/.',
            'PEN' => 'S/',
            'PGK' => 'K',
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PRB' => '&#x440;.',
            'PYG' => '&#8370;',
            'QAR' => '&#x631;.&#x642;',
            'RMB' => '&yen;',
            'RON' => 'lei',
            'RSD' => '&#1088;&#1089;&#1076;',
            'RUB' => '&#8381;',
            'RWF' => 'Fr',
            'SAR' => '&#x631;.&#x633;',
            'SBD' => '&#36;',
            'SCR' => '&#x20a8;',
            'SDG' => '&#x62c;.&#x633;.',
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&pound;',
            'SLL' => 'Le',
            'SOS' => 'Sh',
            'SRD' => '&#36;',
            'SSP' => '&pound;',
            'STN' => 'Db',
            'SYP' => '&#x644;.&#x633;',
            'SZL' => 'L',
            'THB' => '&#3647;',
            'TJS' => '&#x405;&#x41c;',
            'TMT' => 'm',
            'TND' => '&#x62f;.&#x62a;',
            'TOP' => 'T&#36;',
            'TRY' => '&#8378;',
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'TZS' => 'Sh',
            'UAH' => '&#8372;',
            'UGX' => 'UGX',
            'USD' => '&#36;',
            'UYU' => '&#36;',
            'UZS' => 'UZS',
            'VEF' => 'Bs F',
            'VES' => 'Bs.S',
            'VND' => '&#8363;',
            'VUV' => 'Vt',
            'WST' => 'T',
            'XAF' => 'CFA',
            'XCD' => '&#36;',
            'XOF' => 'CFA',
            'XPF' => 'Fr',
            'YER' => '&#xfdfc;',
            'ZAR' => '&#82;',
            'ZMW' => 'ZK',
        )
    );

    return apply_filters('mglut_currency_symbols', $symbols);
}

/**
 * Get Currency symbol.
 *
 * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
 *
 * @param string $currency Currency. (default: '').
 *
 * @return string
 */
function mglut_get_currency_symbol($currency = '')
{
    if ( ! $currency) {
        $currency = mglut_get_currency();
    }

    $symbols = mglut_get_currency_symbols();

    $currency_symbol = isset($symbols[$currency]) ? $symbols[$currency] : '';

    return apply_filters('mglut_currency_symbol', $currency_symbol, $currency);
}


/**
 * Get the name of a currency
 *
 * @param string $code The currency code
 *
 * @return string The currency's name
 */
function mglut_get_currency_name($code = '')
{
    if ( ! $code) {
        $code = mglut_get_currency();
    }

    $currencies = mglut_get_currencies();
    $name       = isset($currencies[$code]) ? $currencies[$code] : $code;

    return apply_filters('mglut_currency_name', $name);
}

/**
 * Accepts an amount (ideally from the database, unmodified) and formats it
 * for display. The amount itself is formatted and the currency prefix/suffix
 * is applied and positioned.
 *
 * @param string $amount
 * @param string $currency
 *
 * @return string
 *
 */


/**
 *
 * @param $amount
 *
 * @return string
 */


/**
 * Converts price, fee or amount in cent to decimal
 *
 * @param $amount
 *
 * @return string
 */
function mglut_cent_to_decimal($amount)
{
    return mglut_sanitize_amount(Calculator::init($amount)->dividedBy('100')->val());
}


/**
 * Converts a date/time to UTC
 */
function mglut_local_datetime_to_utc($date, $format = 'Y-m-d H:i:s')
{
    try {
        $a = new DateTime($date, wp_timezone());
        $a->setTimezone(new DateTimeZone('UTC'));

        return $a->format($format);

    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Formats UTC datetime according to WordPress date/time format and using WordPress site timezone.
 *
 * Expects time/timestamp to be in UTC
 *
 * @param string $timestamp timestamp or datetime in UTC
 *
 * @param string $format
 *
 * @return string datetime in WP timezone
 */
function mglut_format_date_time($timestamp, $format = '')
{
    /**
     * force strtotime to use date as UTC.
     * @see https://stackoverflow.com/a/6275660/2648410
     */
    $timestamp = ! is_numeric($timestamp) ? strtotime($timestamp . ' UTC') : $timestamp;

    $format = empty($format) ? get_option('date_format') . ' ' . get_option('time_format') : $format;

    return wp_date($format, $timestamp);
}

/**
 * Formats UTC date according to WordPress date format and using WordPress site timezone.
 *
 * @param string $timestamp timestamp or datetime in UTC
 * @param string $format
 *
 * @return string date in WP timezone
 */
function mglut_format_date($timestamp, $format = '')
{
    if (empty($timestamp)) return '';

    /**
     * force strtotime to use date as UTC.
     * @see https://stackoverflow.com/a/6275660/2648410
     */
    $timestamp = ! is_numeric($timestamp) ? strtotime($timestamp . ' UTC') : $timestamp;

    $format = empty($format) ? get_option('date_format') : $format;

    return wp_date($format, $timestamp);
}


function mglut_business_name()
{
    return mglut_settings_by_key('business_name', '', true);
}

function mglut_business_address($default = '')
{
    return mglut_settings_by_key('business_address', $default, true);
}

function mglut_business_city($default = '')
{
    return mglut_settings_by_key('business_city', $default, true);
}

function mglut_business_country($default = '')
{
    return mglut_settings_by_key('business_country', $default, true);
}

function mglut_business_state($default = '')
{
    return mglut_settings_by_key('business_state', $default, true);
}

function mglut_business_postal_code($default = '')
{
    return mglut_settings_by_key('business_postal_code', $default, true);
}

function mglut_business_full_address()
{
    $billing_address = mglut_business_address();

    if (empty($billing_address)) return '';

    $business_country = mglut_business_country();

    $state = mglut_var(mglut_array_of_world_states($business_country), mglut_business_state(), mglut_business_state(), true);

    $address   = [trim($billing_address)];
    $address[] = trim(mglut_business_city() . ' ' . $state);
    $address[] = mglut_business_postal_code();
    $address[] = mglut_array_of_world_countries($business_country);

    return implode(', ', array_filter($address));
}

function mglut_business_tax_id($default = '')
{
    return mglut_settings_by_key('business_tin', $default, true);
}

