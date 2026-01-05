<?php
defined('BASEPATH') OR exit('');

/**
 * Thermal Printer Library
 * Handles thermal receipt printing using ESC/POS protocol
 * 
 * @author Mobile Shop POS
 * @date 2024-12-27
 */

// Load Composer autoloader if available
if (file_exists(FCPATH . 'vendor/autoload.php')) {
    require_once FCPATH . 'vendor/autoload.php';
}

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

class Thermal_printer {
    
    protected $CI;
    protected $printer;
    protected $printerType;
    protected $printerAddress;
    protected $shopName;
    protected $shopAddress;
    protected $shopPhone;
    
    public function __construct() {
        $this->CI =& get_instance();
        
        // Load configuration from .env or config
        $this->printerType = getenv('PRINTER_TYPE') ?: 'network'; // network, windows, file
        $this->printerAddress = getenv('PRINTER_ADDRESS') ?: '192.168.1.100';
        $this->shopName = getenv('SHOP_NAME') ?: 'Mobile Shop POS';
        $this->shopAddress = getenv('SHOP_ADDRESS') ?: 'Shop Address Here';
        $this->shopPhone = getenv('SHOP_PHONE') ?: '0300-1234567';
    }
    
    /**
     * Initialize printer connection
     * @return bool
     */
    private function initPrinter() {
        try {
            if (!class_exists('Mike42\Escpos\Printer')) {
                log_message('error', 'ESC/POS library not installed. Run: composer require mike42/escpos-php');
                return FALSE;
            }
            
            if ($this->printerType === 'network') {
                $connector = new NetworkPrintConnector($this->printerAddress, 9100);
            } elseif ($this->printerType === 'windows') {
                $connector = new WindowsPrintConnector($this->printerAddress);
            } else {
                // File connector for testing
                $connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector("php://stdout");
            }
            
            $this->printer = new Printer($connector);
            return TRUE;
            
        } catch (Exception $e) {
            log_message('error', 'Printer initialization failed: ' . $e->getMessage());
            return FALSE;
        }
    }
    
    /**
     * Print receipt
     * @param array $transactionData - transaction details
     * @return bool
     */
    public function printReceipt($transactionData) {
        if (!$this->initPrinter()) {
            return $this->generatePDFReceipt($transactionData);
        }
        
        try {
            // Header
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 2);
            $this->printer->text($this->shopName . "\n");
            $this->printer->setTextSize(1, 1);
            $this->printer->setEmphasis(false);
            $this->printer->text($this->shopAddress . "\n");
            $this->printer->text("Tel: " . $this->shopPhone . "\n");
            $this->printer->feed();
            
            // Receipt title
            $this->printer->setEmphasis(true);
            $this->printer->text("SALES RECEIPT\n");
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            
            // Transaction details
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Ref: " . $transactionData['ref'] . "\n");
            $this->printer->text("Date: " . date('d M Y, h:i A', strtotime($transactionData['date'])) . "\n");
            $this->printer->text("Cashier: " . $transactionData['staff_name'] . "\n");
            
            if (!empty($transactionData['customer_name'])) {
                $this->printer->text("Customer: " . $transactionData['customer_name'] . "\n");
                if (!empty($transactionData['customer_phone'])) {
                    $this->printer->text("Phone: " . $transactionData['customer_phone'] . "\n");
                }
            }
            
            $this->printer->feed();
            $this->printer->text(str_repeat("-", 48) . "\n");
            
            // Items header
            $this->printer->text(sprintf("%-20s %3s %10s %12s\n", "Item", "Qty", "Price", "Total"));
            $this->printer->text(str_repeat("-", 48) . "\n");
            
            // Items
            foreach ($transactionData['items'] as $item) {
                $itemName = substr($item['name'], 0, 20);
                $this->printer->text(sprintf("%-20s %3d %10s %12s\n", 
                    $itemName,
                    $item['quantity'],
                    "Rs." . number_format($item['unitPrice'], 0),
                    "Rs." . number_format($item['totalPrice'], 0)
                ));
                
                // Print IMEI if available
                if (!empty($item['imei'])) {
                    $this->printer->text("  IMEI: " . $item['imei'] . "\n");
                }
                
                // Print warranty if available
                if (!empty($item['warranty_months']) && $item['warranty_months'] > 0) {
                    $this->printer->text("  Warranty: " . $item['warranty_months'] . " months\n");
                }
            }
            
            $this->printer->text(str_repeat("-", 48) . "\n");
            
            // Totals
            $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
            $this->printer->text("Subtotal: Rs." . number_format($transactionData['subtotal'], 2) . "\n");
            
            if ($transactionData['discount_amount'] > 0) {
                $this->printer->text("Discount (" . $transactionData['discount_percentage'] . "%): -Rs." . number_format($transactionData['discount_amount'], 2) . "\n");
            }
            
            if ($transactionData['vat_amount'] > 0) {
                $this->printer->text("VAT (" . $transactionData['vat_percentage'] . "%): Rs." . number_format($transactionData['vat_amount'], 2) . "\n");
            }
            
            if (!empty($transactionData['trade_in_value']) && $transactionData['trade_in_value'] > 0) {
                $this->printer->text("Trade-In: -Rs." . number_format($transactionData['trade_in_value'], 2) . "\n");
            }
            
            $this->printer->feed();
            $this->printer->setEmphasis(true);
            $this->printer->setTextSize(2, 1);
            $this->printer->text("TOTAL: Rs." . number_format($transactionData['grand_total'], 2) . "\n");
            $this->printer->setTextSize(1, 1);
            $this->printer->setEmphasis(false);
            $this->printer->feed();
            
            // Payment details
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Payment Method: " . strtoupper($transactionData['payment_method']) . "\n");
            
            if ($transactionData['payment_method'] === 'cash' || $transactionData['payment_method'] === 'pos') {
                $this->printer->text("Amount Tendered: Rs." . number_format($transactionData['amount_tendered'], 2) . "\n");
                $this->printer->text("Change Due: Rs." . number_format($transactionData['change_due'], 2) . "\n");
            } elseif ($transactionData['payment_method'] === 'partial') {
                $this->printer->text("Paid Now: Rs." . number_format($transactionData['paid_amount'], 2) . "\n");
                $this->printer->text("Credit: Rs." . number_format($transactionData['credit_amount'], 2) . "\n");
            } elseif ($transactionData['payment_method'] === 'credit') {
                $this->printer->text("Credit Amount: Rs." . number_format($transactionData['credit_amount'], 2) . "\n");
            }
            
            $this->printer->feed();
            
            // Footer
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text("Thank you for your business!\n");
            $this->printer->text("Please visit again\n");
            $this->printer->feed();
            
            // Barcode (transaction ref)
            try {
                $this->printer->setBarcodeHeight(50);
                $this->printer->setBarcodeWidth(2);
                $this->printer->barcode($transactionData['ref'], Printer::BARCODE_CODE39);
            } catch (Exception $e) {
                // Barcode failed, just print text
                $this->printer->text($transactionData['ref'] . "\n");
            }
            
            $this->printer->feed(3);
            $this->printer->cut();
            
            // Close printer
            $this->printer->close();
            
            return TRUE;
            
        } catch (Exception $e) {
            log_message('error', 'Receipt printing failed: ' . $e->getMessage());
            
            // Try to close printer connection
            if ($this->printer) {
                try {
                    $this->printer->close();
                } catch (Exception $closeError) {
                    // Ignore close errors
                }
            }
            
            return FALSE;
        }
    }
    
    /**
     * Generate PDF receipt as fallback
     * @param array $transactionData
     * @return bool
     */
    private function generatePDFReceipt($transactionData) {
        // Fallback: Generate HTML receipt that can be printed
        log_message('info', 'Thermal printer unavailable, generating HTML receipt');
        
        // Store receipt HTML in session for display
        $receiptHTML = $this->generateHTMLReceipt($transactionData);
        $_SESSION['last_receipt_html'] = $receiptHTML;
        
        return TRUE;
    }
    
    /**
     * Generate HTML receipt
     * @param array $transactionData
     * @return string
     */
    public function generateHTMLReceipt($transactionData) {
        $html = '<div style="width: 300px; font-family: monospace; font-size: 12px; margin: 0 auto;">';
        $html .= '<div style="text-align: center; font-weight: bold; font-size: 16px;">' . $this->shopName . '</div>';
        $html .= '<div style="text-align: center;">' . $this->shopAddress . '</div>';
        $html .= '<div style="text-align: center;">Tel: ' . $this->shopPhone . '</div>';
        $html .= '<br>';
        $html .= '<div style="text-align: center; font-weight: bold;">SALES RECEIPT</div>';
        $html .= '<br>';
        $html .= '<div>Ref: ' . $transactionData['ref'] . '</div>';
        $html .= '<div>Date: ' . date('d M Y, h:i A', strtotime($transactionData['date'])) . '</div>';
        $html .= '<div>Cashier: ' . $transactionData['staff_name'] . '</div>';
        
        if (!empty($transactionData['customer_name'])) {
            $html .= '<div>Customer: ' . $transactionData['customer_name'] . '</div>';
        }
        
        $html .= '<hr>';
        $html .= '<table style="width: 100%; font-size: 11px;">';
        $html .= '<tr><th align="left">Item</th><th>Qty</th><th align="right">Price</th><th align="right">Total</th></tr>';
        
        foreach ($transactionData['items'] as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td align="center">' . $item['quantity'] . '</td>';
            $html .= '<td align="right">Rs.' . number_format($item['unitPrice'], 0) . '</td>';
            $html .= '<td align="right">Rs.' . number_format($item['totalPrice'], 0) . '</td>';
            $html .= '</tr>';
            
            if (!empty($item['imei'])) {
                $html .= '<tr><td colspan="4" style="font-size: 10px;">IMEI: ' . $item['imei'] . '</td></tr>';
            }
        }
        
        $html .= '</table>';
        $html .= '<hr>';
        $html .= '<div style="text-align: right;">Subtotal: Rs.' . number_format($transactionData['subtotal'], 2) . '</div>';
        
        if ($transactionData['discount_amount'] > 0) {
            $html .= '<div style="text-align: right;">Discount: -Rs.' . number_format($transactionData['discount_amount'], 2) . '</div>';
        }
        
        if ($transactionData['vat_amount'] > 0) {
            $html .= '<div style="text-align: right;">VAT: Rs.' . number_format($transactionData['vat_amount'], 2) . '</div>';
        }
        
        $html .= '<div style="text-align: right; font-weight: bold; font-size: 14px;">TOTAL: Rs.' . number_format($transactionData['grand_total'], 2) . '</div>';
        $html .= '<br>';
        $html .= '<div style="text-align: center;">Thank you for your business!</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Test printer connection
     * @return array
     */
    public function testPrinter() {
        if (!$this->initPrinter()) {
            return [
                'status' => 0,
                'message' => 'Failed to connect to printer. Check printer address and connection.'
            ];
        }
        
        try {
            $this->printer->text("Printer Test\n");
            $this->printer->text("Connection successful!\n");
            $this->printer->feed(2);
            $this->printer->cut();
            $this->printer->close();
            
            return [
                'status' => 1,
                'message' => 'Printer test successful!'
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 0,
                'message' => 'Printer test failed: ' . $e->getMessage()
            ];
        }
    }
}
