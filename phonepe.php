<?php
// includes/phonepe.php - PhonePe Payment Gateway Integration

class PhonePePayment {
    private $merchantId;
    private $saltKey;
    private $saltIndex;
    private $hostUrl;
    private $mode;
    
    public function __construct() {
        $this->merchantId = PHONEPE_MERCHANT_ID;
        $this->saltKey = PHONEPE_SALT_KEY;
        $this->saltIndex = PHONEPE_SALT_INDEX;
        $this->hostUrl = PHONEPE_HOST_URL;
        $this->mode = PHONEPE_MODE;
    }
    
    /**
     * Initiate payment request
     */
    public function initiatePayment($orderId, $amount, $mobile, $name, $email) {
        try {
            // PhonePe expects amount in paise (multiply by 100)
            $amountInPaise = $amount * 100;
            
            // Prepare payment payload
            $paymentData = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $orderId,
                'merchantUserId' => 'USER_' . $orderId,
                'amount' => $amountInPaise,
                'redirectUrl' => PAYMENT_CALLBACK_URL . '?order_id=' . $orderId,
                'redirectMode' => 'POST',
                'callbackUrl' => PAYMENT_WEBHOOK_URL,
                'mobileNumber' => $mobile,
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE'
                ]
            ];
            
            // Add user details for better tracking
            if (!empty($email)) {
                $paymentData['merchantUserId'] = $email;
            }
            
            // Encode payload
            $jsonEncode = json_encode($paymentData);
            $base64Encode = base64_encode($jsonEncode);
            
            // Generate checksum
            $checksum = $this->generateChecksum($base64Encode);
            
            // Prepare request headers
            $headers = [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum
            ];
            
            // API endpoint
            $apiEndpoint = $this->hostUrl . '/pg/v1/pay';
            
            // Request body
            $requestBody = json_encode([
                'request' => $base64Encode
            ]);
            
            // Make API call
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            $responseData = json_decode($response, true);
            
            // Log the response for debugging
            if (TEST_MODE) {
                error_log("PhonePe Response: " . print_r($responseData, true));
            }
            
            if ($httpCode == 200 && isset($responseData['success']) && $responseData['success'] === true) {
                // Payment URL from response
                $paymentUrl = $responseData['data']['instrumentResponse']['redirectInfo']['url'] ?? '';
                
                return [
                    'success' => true,
                    'payment_url' => $paymentUrl,
                    'transaction_id' => $orderId,
                    'response' => $responseData
                ];
            } else {
                $errorMessage = $responseData['message'] ?? 'Payment initiation failed';
                throw new Exception($errorMessage);
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Verify payment callback
     */
    public function verifyPayment($response) {
        try {
            // PhonePe sends base64 encoded response
            $base64Response = $response;
            
            // Verify checksum
            $receivedChecksum = $_SERVER['HTTP_X_VERIFY'] ?? $_REQUEST['x-verify'] ?? '';
            $calculatedChecksum = $this->generateChecksum($base64Response);
            
            if ($receivedChecksum !== $calculatedChecksum) {
                throw new Exception('Invalid checksum');
            }
            
            // Decode response
            $decodedResponse = base64_decode($base64Response);
            $responseData = json_decode($decodedResponse, true);
            
            return [
                'success' => true,
                'data' => $responseData
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Check payment status
     */
    public function checkPaymentStatus($transactionId) {
        try {
            // API endpoint for status check
            $apiEndpoint = $this->hostUrl . '/pg/v1/status/' . $this->merchantId . '/' . $transactionId;
            
            // Generate checksum for status API
            $checksumString = '/pg/v1/status/' . $this->merchantId . '/' . $transactionId . $this->saltKey;
            $checksum = hash('sha256', $checksumString) . '###' . $this->saltIndex;
            
            // Prepare headers
            $headers = [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum,
                'X-MERCHANT-ID: ' . $this->merchantId
            ];
            
            // Make API call
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }
            
            curl_close($ch);
            
            $responseData = json_decode($response, true);
            
            if ($httpCode == 200 && isset($responseData['success']) && $responseData['success'] === true) {
                $paymentStatus = $responseData['data']['state'] ?? 'PENDING';
                
                return [
                    'success' => true,
                    'status' => $paymentStatus,
                    'data' => $responseData['data']
                ];
            } else {
                throw new Exception('Failed to fetch payment status');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate checksum
     */
    private function generateChecksum($base64String) {
        $checksumString = $base64String . '/pg/v1/pay' . $this->saltKey;
        $checksum = hash('sha256', $checksumString);
        return $checksum . '###' . $this->saltIndex;
    }
    
    /**
     * Process refund
     */
    public function processRefund($originalTransactionId, $refundAmount, $refundTransactionId) {
        try {
            // PhonePe expects amount in paise
            $refundAmountInPaise = $refundAmount * 100;
            
            // Prepare refund payload
            $refundData = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $refundTransactionId,
                'originalTransactionId' => $originalTransactionId,
                'amount' => $refundAmountInPaise,
                'callbackUrl' => PAYMENT_WEBHOOK_URL
            ];
            
            // Encode payload
            $jsonEncode = json_encode($refundData);
            $base64Encode = base64_encode($jsonEncode);
            
            // Generate checksum for refund
            $checksumString = $base64Encode . '/pg/v1/refund' . $this->saltKey;
            $checksum = hash('sha256', $checksumString) . '###' . $this->saltIndex;
            
            // Prepare headers
            $headers = [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum
            ];
            
            // API endpoint
            $apiEndpoint = $this->hostUrl . '/pg/v1/refund';
            
            // Request body
            $requestBody = json_encode([
                'request' => $base64Encode
            ]);
            
            // Make API call
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            curl_close($ch);
            
            $responseData = json_decode($response, true);
            
            if ($httpCode == 200 && isset($responseData['success']) && $responseData['success'] === true) {
                return [
                    'success' => true,
                    'refund_id' => $refundTransactionId,
                    'data' => $responseData
                ];
            } else {
                throw new Exception($responseData['message'] ?? 'Refund failed');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}