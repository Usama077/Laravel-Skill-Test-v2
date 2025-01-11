<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $filePath = 'products.json';

    public function index()
    {
        return view('product_data');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data['datetime_submitted'] = now()->toDateTimeString();
        $data['total_value'] = $data['quantity'] * $data['price'];

        $fileData = $this->readData();
        $fileData[] = $data;

        $this->writeData($fileData);

        return response()->json(['success' => true]);
    }
    public function listProducts()
    {
        $data = json_decode(Storage::get($this->filePath), true) ?? [];
        return response()->json($data);
    }
    public function update(Request $request, $index)
    {
        $data = $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data['datetime_submitted'] = now()->toDateTimeString();
        $data['total_value'] = $data['quantity'] * $data['price'];

        $fileData = $this->readData();
        $fileData[$index] = $data;

        $this->writeData($fileData);

        return response()->json(['success' => true]);
    }

    private function readData()
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }

        return json_decode(Storage::get($this->filePath), true);
    }

    private function writeData(array $data)
    {
        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
