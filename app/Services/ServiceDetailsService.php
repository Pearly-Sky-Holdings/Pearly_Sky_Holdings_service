<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ItemDetails;
use App\Models\Order;
use App\Models\PackageDetail;
use App\Models\PersonalInformations;
use App\Models\ReStockingChecklistDetails;
use App\Models\Service;
use App\Models\ServiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ServiceDetailsService
{
    public function save(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'customer_id' => 'sometimes',
                'customer' => 'required_without:customer_id|array',
                'service_id' => 'required',
                'price' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'property_size' => 'nullable|string',
                'duration' => 'nullable|integer',
                'number_of_cleaners' => 'nullable|integer',
                'note' => 'nullable|string',
                'request_gender' => 'nullable|string',
                'request_language' => 'nullable|string',
                'business_property' => 'nullable|string',
                'frequency' => 'nullable|string',
                'cleaning_solvents' => 'nullable|string',
                'Equipment' => 'nullable|string',
                'personal_information' => 'array',
                'reStock_details' => 'array',
                'cleaning_item' => 'array',
                'package_details' => 'array',
            ]);

            // Execute transaction in a separate method
            $result = $this->executeTransaction($validatedData);

            // Send email after successful transaction
            $this->sendEmail($result['customer']->email, $result['customerId']);

            return response()->json([
                'status' => 'success',
                'message' => 'Service details saved successfully',
                'data' => [
                    'service_detail' => $result['serviceDetail'],
                    'order' => $result['order'],
                    'customer' => $result['customer']
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], status: 500);
        }
    }


    private function executeTransaction(array $validatedData)
    {
        try {
            DB::beginTransaction();

            // Handle customer creation or update
            if (!isset($validatedData['customer_id'])) {
                $customer = Customer::create($validatedData['customer']);
                $result = DB::table('customers')
                    ->Where('email', 'LIKE', '%' . $customer->email . '%')
                    ->get()->first();
                $customerId = $result->customer_id;
            } else {
                $customer = Customer::find($validatedData['customer_id']);
                $customer->update($validatedData['customer']);
                $customerId = $validatedData['customer_id'];
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customerId,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'price' => ($validatedData['price']),
                'status' => 'inactive'
            ]);

            // Create service detail
            $serviceDetail = ServiceDetails::create([
                'order_id' => $order->order_id,
                'customer_id' => $customerId,
                'service_id' => $validatedData['service_id'],
                'price' => $validatedData['price'],
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'property_size' => $validatedData['property_size'] ?? null,
                'duration' => $validatedData['duration'] ?? null,
                'number_of_cleaners' => $validatedData['number_of_cleaners'] ?? 1,
                'note' => $validatedData['note'] ?? null,
                'request_gender' => $validatedData['person_type'] ?? null,
                'frequency' => $validatedData['frequency'] ?? null,
                'request_language' => $validatedData['request_language'] ?? 'en',
                'business_property' => $validatedData['business_property'] ?? null,
                'cleaning_solvents' => $validatedData['cleaning_solvents'] ?? null,
                'Equipment' => $validatedData['Equipment'] ?? null,
                'status' => 'pending'
            ]);

            // Save personal information if provided
            if (isset($validatedData['personal_information'])) {
                $personalInformation = $validatedData['personal_information'];
                $personalInformation['service_detail_id'] = $serviceDetail->id;
                PersonalInformations::create($personalInformation);
            }

            // Save package details if provided
            if (isset($validatedData['package_details'])) {
                foreach ($validatedData['package_details'] as $packageDetail) {
                    PackageDetail::create([
                        'package_id' => $packageDetail['package_id'],
                        'service_detail_id' => $serviceDetail->id,
                        'price' => $packageDetail['price'] ?? null,
                        'qty' => $packageDetail['qty'] ?? null,
                    ]);
                }
            }

            // Save reStock details if provided
            if (isset($validatedData['reStock_details'])) {
                foreach ($validatedData['reStock_details'] as $reStockDetail) {
                    ReStockingChecklistDetails::create([
                        're_stocking_checklist_id' => $reStockDetail['re_stocking_checklist_id'],
                        'service_detail_id' => $serviceDetail->id,
                    ]);
                }
            }

            // Save item details if provided
            if (isset($validatedData['cleaning_item'])) {
                foreach ($validatedData['cleaning_item'] as $item) {
                    ItemDetails::create([
                        'item_id' => $item['id'],
                        'service_detail_id' => $serviceDetail->id,
                        'qty' => $item['qty'],
                        'price' => $item['price']
                    ]);
                }
            }

            // Commit transaction
            DB::commit();

            return [
                'customer' => $customer,
                'customerId' => $customerId,
                'order' => $order,
                'serviceDetail' => $serviceDetail
            ];

        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            throw $e; // Re-throw the exception to be caught in the save method
        }
    }

   
    private function sendEmail($email, $customerId)
    {
        try {
            $customer = Customer::find($customerId);
            $latestOrder = Order::where('customer_id', $customerId)->latest()->first();
            $serviceDetail = ServiceDetails::where('order_id', $latestOrder->order_id)->first();
            $service = Service::find($serviceDetail->service_id);

            // Get package details
            $packageDetails = PackageDetail::where('service_detail_id', $serviceDetail->id)
                ->with('package')
                ->get();

            // Generate QR code and save as an image
            // $qrCodePath = storage_path("app/public/qrcodes/customer_{$customerId}.png");
            // QrCode::format('png')
            //     ->size(200)
            //     ->generate($customerId, $qrCodePath);

            // Prepare data for email template
            $data = [
                'customer' => $customer,
                'order' => $latestOrder,
                'serviceDetail' => $serviceDetail,
                'service' => $service,
                'packageDetails' => $packageDetails
            ];

            // Send email with QR code as an attachment
            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation($data));

            Log::info("Email sent successfully to customer: {$email}");

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send email to customer: {$email}. Error: " . $e->getMessage());
            return false;
        }
    }
}