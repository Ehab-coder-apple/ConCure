<?php

namespace App\Services;

class WordDocumentService
{
    /**
     * Generate Word document for nutrition plan
     */
    public function generateNutritionPlan($dietPlan, $nutritionalTotals)
    {
        // Create Word document content as HTML that can be opened by Word
        $html = $this->generateWordHtml($dietPlan, $nutritionalTotals);
        
        return $html;
    }

    /**
     * Generate HTML content optimized for Word
     */
    private function generateWordHtml($dietPlan, $nutritionalTotals)
    {
        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word">
    <meta name="Originator" content="Microsoft Word">
    <title>Daily Meal Plan</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>90</w:Zoom>
            <w:DoNotPromptForConvert/>
            <w:DoNotShowRevisions/>
            <w:DoNotPrintRevisions/>
            <w:DisplayHorizontalDrawingGridEvery>0</w:DisplayHorizontalDrawingGridEvery>
            <w:DisplayVerticalDrawingGridEvery>2</w:DisplayVerticalDrawingGridEvery>
            <w:UseMarginsForDrawingGridOrigin/>
            <w:ValidateAgainstSchemas/>
            <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
            <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
            <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
            <w:Compatibility>
                <w:BreakWrappedTables/>
                <w:SnapToGridInCell/>
                <w:WrapTextWithPunct/>
                <w:UseAsianBreakRules/>
            </w:Compatibility>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: A4;
            margin: 1in;
        }
        
        body {
            font-family: "Navshke", "Amiri", "Arabic Typesetting", "Traditional Arabic", Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            direction: ltr;
        }
        
        .kurdish {
            font-family: "Navshke", "Amiri", "Arabic Typesetting", "Traditional Arabic", Arial, sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14pt;
            line-height: 1.8;
            unicode-bidi: embed;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20pt;
            border-bottom: 2pt solid #20B2AA;
            padding-bottom: 10pt;
        }
        
        .header h1 {
            color: #20B2AA;
            font-size: 20pt;
            font-weight: bold;
            margin: 0;
        }
        
        .patient-info {
            margin-bottom: 15pt;
            padding: 10pt;
            border: 1pt solid #ccc;
            background-color: #f9f9f9;
        }
        
        .meal-section {
            margin-bottom: 15pt;
            border: 1pt solid #ddd;
            page-break-inside: avoid;
        }
        
        .meal-header {
            background-color: #20B2AA;
            color: white;
            padding: 8pt 12pt;
            font-size: 14pt;
            font-weight: bold;
        }
        
        .food-item {
            padding: 8pt 12pt;
            border-bottom: 1pt solid #eee;
        }
        
        .food-item:last-child {
            border-bottom: none;
        }
        
        .food-name {
            font-weight: bold;
            margin-bottom: 4pt;
        }
        
        .food-details {
            color: #666;
            font-size: 10pt;
        }
        
        .meal-total {
            background-color: #f0f8ff;
            padding: 8pt 12pt;
            font-weight: bold;
            color: #20B2AA;
            border-top: 1pt solid #20B2AA;
        }
        
        .summary {
            margin-top: 20pt;
            padding: 15pt;
            border: 2pt solid #20B2AA;
            background-color: #f0f8ff;
        }
        
        .summary h3 {
            color: #20B2AA;
            margin-top: 0;
            text-align: center;
            font-size: 16pt;
        }
        
        .summary-item {
            margin: 8pt 0;
            padding: 4pt 0;
            border-bottom: 1pt dotted #ccc;
        }
        
        .summary-label {
            font-weight: bold;
            display: inline-block;
            width: 60%;
        }
        
        .summary-value {
            display: inline-block;
            width: 35%;
            text-align: right;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        td {
            padding: 4pt;
            vertical-align: top;
        }
    </style>
</head>
<body>';

        // Header
        $html .= '<div class="header">
            <h1>Daily Meal Plan</h1>
        </div>';

        // Patient Info
        $html .= '<div class="patient-info">
            <strong>Patient:</strong> ' . htmlspecialchars($dietPlan->patient->name) . '<br>
            <strong>Plan Number:</strong> ' . htmlspecialchars($dietPlan->plan_number) . '<br>
            <strong>Date:</strong> ' . $dietPlan->created_at->format('Y-m-d') . '<br>
            <strong>Doctor:</strong> ' . htmlspecialchars($dietPlan->doctor->name) . '
        </div>';

        // Meals
        $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
        
        foreach ($mealTypes as $mealType) {
            $meals = $dietPlan->meals->where('meal_type', $mealType);
            
            if ($meals->count() > 0) {
                $html .= '<div class="meal-section">
                    <div class="meal-header">' . ucfirst($mealType) . '</div>';
                
                $mealCalories = 0;
                
                foreach ($meals as $meal) {
                    foreach ($meal->foods as $mealFood) {
                        $food = $mealFood->food;
                        $quantity = $mealFood->quantity;
                        $calories = ($food->calories * $quantity) / 100;
                        $protein = ($food->protein * $quantity) / 100;
                        $carbs = ($food->carbohydrates * $quantity) / 100;
                        $fat = ($food->fat * $quantity) / 100;
                        
                        $mealCalories += $calories;
                        
                        $html .= '<div class="food-item">
                            <div class="food-name kurdish">' . $mealFood->food_name . '</div>
                            <div class="food-details">
                                ' . $mealFood->quantity . ' ' . $mealFood->unit . ' | 
                                ' . number_format($calories, 0) . ' cal | 
                                ' . number_format($protein, 1) . 'g protein | 
                                ' . number_format($carbs, 1) . 'g carbs | 
                                ' . number_format($fat, 1) . 'g fat
                            </div>
                        </div>';
                    }
                }
                
                $html .= '<div class="meal-total">
                    Total: ' . number_format($mealCalories, 0) . ' calories
                </div></div>';
            }
        }

        // Summary
        $html .= '<div class="summary">
            <h3>Daily Nutritional Summary</h3>
            <div class="summary-item">
                <span class="summary-label">Total Calories:</span>
                <span class="summary-value">' . number_format($nutritionalTotals['calories'], 0) . ' cal</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Protein:</span>
                <span class="summary-value">' . number_format($nutritionalTotals['protein'], 1) . 'g</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Carbohydrates:</span>
                <span class="summary-value">' . number_format($nutritionalTotals['carbs'], 1) . 'g</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Fat:</span>
                <span class="summary-value">' . number_format($nutritionalTotals['fat'], 1) . 'g</span>
            </div>
        </div>';

        $html .= '<div style="margin-top: 20pt; text-align: center; font-size: 8pt; color: #666; border-top: 1pt solid #ddd; padding-top: 10pt;">
            Generated on ' . now()->format('Y-m-d H:i:s') . ' | ConCure Clinic Management System
        </div>';

        $html .= '</body></html>';

        return $html;
    }
}
