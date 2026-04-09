<?php
/**
 * ===========================================
 * EXPORT DES INSCRIPTIONS (CSV / EXCEL)
 * ===========================================
 * 
 * Script d'export des inscriptions aide aux devoirs.
 * Supporte deux formats : CSV et Excel (.xlsx)
 * 
 * Utilisation :
 * - export_inscriptions.php?format=csv
 * - export_inscriptions.php?format=excel
 */

// Charger PhpSpreadsheet
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

session_start();
require_once '../includes/db.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Format demandé
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// ========== RÉCUPÉRATION DES INSCRIPTIONS ==========
$query = $pdo->query("SELECT * FROM messages WHERE message LIKE '%INSCRIPTION AIDE AUX DEVOIRS%' ORDER BY date_envoi DESC");
$inscriptions_raw = $query->fetchAll();

// Parser les inscriptions
$inscriptions = [];
foreach ($inscriptions_raw as $msg) {
    $inscription = [
        'Prénom' => '',
        'Nom' => '',
        'Classe' => '',
        'Adresse' => '',
        'Téléphone' => '',
        'Email Parent' => '',
        'Date Inscription' => date('d/m/Y H:i', strtotime($msg['date_envoi']))
    ];
    
    if (preg_match('/Enfant : (.+?) (.+)/', $msg['message'], $m)) {
        $inscription['Nom'] = trim($m[1]);
        $inscription['Prénom'] = trim($m[2]);
    }
    if (preg_match('/Classe : (.+)/', $msg['message'], $m)) {
        $inscription['Classe'] = trim($m[1]);
    }
    if (preg_match('/Adresse : (.+)/', $msg['message'], $m)) {
        $inscription['Adresse'] = trim($m[1]);
    }
    if (preg_match('/Téléphone : (.+)/', $msg['message'], $m)) {
        $inscription['Téléphone'] = trim($m[1]);
    }
    if (preg_match('/Email parent : (.+)/', $msg['message'], $m)) {
        $inscription['Email Parent'] = trim($m[1]);
    }
    
    $inscriptions[] = $inscription;
}

// Nom du fichier
$filename = 'inscriptions_aide_devoirs_' . date('Y-m-d');

// ========== EXPORT CSV ==========
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    // BOM UTF-8 pour Excel
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // En-têtes
    if (!empty($inscriptions)) {
        fputcsv($output, array_keys($inscriptions[0]), ';');
    }
    
    // Données
    foreach ($inscriptions as $row) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit();
}

// ========== EXPORT EXCEL ==========
if ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Inscriptions');
    
    // Style en-têtes
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667EEA']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    
    // Style données
    $dataStyle = [
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
    ];
    
    // En-têtes
    $headers = ['Prénom', 'Nom', 'Classe', 'Adresse', 'Téléphone', 'Email Parent', 'Date Inscription'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }
    $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
    $sheet->getRowDimension(1)->setRowHeight(25);
    
    // Données
    $row = 2;
    foreach ($inscriptions as $inscription) {
        $sheet->setCellValue('A' . $row, $inscription['Prénom']);
        $sheet->setCellValue('B' . $row, $inscription['Nom']);
        $sheet->setCellValue('C' . $row, $inscription['Classe']);
        $sheet->setCellValue('D' . $row, $inscription['Adresse']);
        $sheet->setCellValue('E' . $row, $inscription['Téléphone']);
        $sheet->setCellValue('F' . $row, $inscription['Email Parent']);
        $sheet->setCellValue('G' . $row, $inscription['Date Inscription']);
        
        // Alternance couleurs
        if ($row % 2 == 0) {
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F3F4F6');
        }
        $row++;
    }
    
    // Bordures sur toutes les données
    $lastRow = $row - 1;
    if ($lastRow >= 2) {
        $sheet->getStyle('A2:G' . $lastRow)->applyFromArray($dataStyle);
    }
    
    // Titre du document
    $spreadsheet->getProperties()
        ->setCreator('Association Aujourd\'hui vers Demain')
        ->setTitle('Liste des inscriptions - Aide aux devoirs')
        ->setSubject('Export inscriptions');
    
    // Téléchargement
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

// Format non reconnu
header("Location: inscriptions.php");
exit();
