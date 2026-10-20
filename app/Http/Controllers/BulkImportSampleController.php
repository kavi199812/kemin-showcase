<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportSampleController extends Controller
{
    /**
     * Download sample CSV template.
     */
    public function download(string $type): StreamedResponse
    {
        if ($type === 'companies') {
            return $this->downloadCompanySample();
        }

        return $this->downloadProductSample();
    }

    protected function downloadProductSample(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="kemin_products_sample.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'name',
                'category',
                'job_number',
                'short_description',
                'description',
                'specs',
                'is_featured',
                'is_active',
                'sort_order',
                'companies',
                'images',
            ]);

            // Sample Row 1
            fputcsv($handle, [
                'Luxury Rigid Ceylon Tea Box',
                'Rigid Boxes',
                'JB-1001',
                'Premium handcrafted rigid packaging box with magnetic closure',
                '<p>Engineered for high-end Ceylon tea gifting with gold foil stamping and velvet insert.</p>',
                'Material: Greyboard 1200gsm; Finishing: Soft-touch Matte; Feature: Gold Stamping & Magnetic Catch',
                '1',
                '1',
                '1',
                'Dilmah Tea, Akbar Brothers',
                'JB-1001.jpg, JB-1001-open.jpg',
            ]);

            // Sample Row 2
            fputcsv($handle, [
                '5-Ply Heavy-Duty Shipping Carton',
                'Corrugated Cartons',
                'JB-1002',
                'Export-grade sea freight corrugated master carton',
                '<p>High-tensile kraft corrugated box engineered for bulk sea and air freight safety.</p>',
                'Material: 5-Ply Kraft BC-Flute; Bursting Test: 14.5 kg/cm2; Dimensions: 450x320x300mm',
                '0',
                '1',
                '2',
                'Dilmah Tea',
                'JB-1002.jpg',
            ]);

            // Sample Row 3
            fputcsv($handle, [
                'Sliding Drawer Garment Box',
                'Rigid Boxes',
                'JB-1003',
                'Custom sliding matchbox-style packaging for apparel',
                '<p>Pull-out drawer design with satin ribbon pull tab, ideal for designer shirts and sarees.</p>',
                'Material: 1000gsm Kappa Board; Liner: 157gsm Art Paper; Accessories: Black Satin Ribbon',
                '1',
                '1',
                '3',
                '',
                'drawer_box_1.png',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    protected function downloadCompanySample(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="kemin_companies_sample.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'name',
                'industry',
                'country',
                'website',
                'description',
                'is_featured',
                'is_active',
                'sort_order',
                'logo',
            ]);

            fputcsv($handle, [
                'Dilmah Tea',
                'Food & Beverage',
                'Sri Lanka',
                'https://www.dilmahtea.com',
                '<p>World renowned pioneer of single-origin Ceylon tea packaging partner.</p>',
                '1',
                '1',
                '1',
                'dilmah_logo.png',
            ]);

            fputcsv($handle, [
                'Akbar Brothers',
                'Tea Export',
                'Sri Lanka',
                'https://www.akbar.com',
                '<p>One of the largest exporters of pure Ceylon tea in bulk and consumer packs.</p>',
                '1',
                '1',
                '2',
                'akbar_logo.svg',
            ]);

            fputcsv($handle, [
                'Brandix Apparel',
                'Apparel & Textiles',
                'Sri Lanka',
                'https://www.brandix.com',
                '<p>Premier apparel solutions provider using eco-friendly corrugated packing boxes.</p>',
                '0',
                '1',
                '3',
                'brandix.jpg',
            ]);

            fclose($handle);
        }, 200, $headers);
    }
}
