<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PackageDetail;
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
            DB::beginTransaction();
            $validatedData = $request->validate([
                'customer_id' => 'sometimes',
                'customer' => 'required_without:customer_id|array',
                'service_id' => 'required',
                'price' => 'required|numeric',
                'date' => 'required|date',
                'time' => 'required',
                'property_size' => 'nullable|string',
                'duration' => 'nullable|integer',
                'number_of_cleaners' => 'nullable|integer',
                'note' => 'nullable|string',
                'person_type' => 'nullable|string',
                'language' => 'nullable|string',
                'business_property' => 'nullable|string',
                'cleaning_solvents' => 'nullable|string',
                'Equipment' => 'nullable|string',
                'package_details' => 'array'
            ]);

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

            $order = Order::create([
                'customer_id' => $customerId,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'price' => "0$",
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
                'person_type' => $validatedData['person_type'] ?? null,
                'language' => $validatedData['language'] ?? 'en',
                'business_property' => $validatedData['business_property'] ?? null,
                'cleaning_solvents' => $validatedData['cleaning_solvents'] ?? null,
                'Equipment' => $validatedData['Equipment'] ?? null,
                'status' => 'pending'
            ]);

            if (isset($validatedData['package_details'])) {
                // Save package details
                foreach ($validatedData['package_details'] as $packageDetail) {
                    PackageDetail::create([
                        'package_id' => $packageDetail['package_id'],
                        'service_detail_id' => $serviceDetail->id,
                        'price' => $packageDetail['price'] ?? null,
                        'qty' => $packageDetail['qty'] ?? null,
                    ]);
                }
            }

            // Commit transaction
            DB::commit();

            $this->sendEmail($customer->email, $customerId);

            return response()->json([
                'status' => 'success',
                'message' => 'Service details saved successfully',
                'data' => [
                    'service_detail' => $serviceDetail,
                    'order' => $order,
                    'customer' => $customer
                ]
            ], 201);


        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], status: 500);
        }
    }


    //send mail 
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
