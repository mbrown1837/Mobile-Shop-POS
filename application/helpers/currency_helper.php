<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Currency Helper
 * 
 * Provides currency formatting functions
 */

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     * 
     * @return string
     */
    function currency_symbol() {
        $CI =& get_instance();
        $CI->config->load('currency', TRUE);
        
        $symbol = $CI->config->item('currency_symbol', 'currency');
        return $symbol ? $symbol : 'Rs.';
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format amount as currency
     * 
     * @param float $amount
     * @param bool $include_symbol
     * @return string
     */
    function format_currency($amount, $include_symbol = TRUE) {
        $CI =& get_instance();
        $CI->config->load('currency', TRUE);
        
        $decimals = $CI->config->item('currency_decimals', 'currency') ?: 2;
        $dec_separator = $CI->config->item('currency_decimal_separator', 'currency') ?: '.';
        $thousands_separator = $CI->config->item('currency_thousands_separator', 'currency') ?: ',';
        $symbol = $CI->config->item('currency_symbol', 'currency') ?: 'Rs.';
        $position = $CI->config->item('currency_symbol_position', 'currency') ?: 'before';
        
        $formatted = number_format($amount, $decimals, $dec_separator, $thousands_separator);
        
        if ($include_symbol) {
            if ($position === 'before') {
                return $symbol . ' ' . $formatted;
            } else {
                return $formatted . ' ' . $symbol;
            }
        }
        
        return $formatted;
    }
}
