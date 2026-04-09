<?php
/**
 * ===========================================
 * GÉNÉRATION DE REÇU PDF
 * ===========================================
 * 
 * Génère un reçu PDF professionnel pour une inscription
 * à l'aide aux devoirs. Utilise TCPDF.
 * 
 * Utilisation : generate_pdf.php?id=123
 */

session_start();
require_once '../includes/db.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// ID de l'inscription
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: inscriptions.php");
    exit();
}

// Récupérer l'inscription
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND message LIKE '%INSCRIPTION AIDE AUX DEVOIRS%'");
$stmt->execute([$id]);
$msg = $stmt->fetch();

if (!$msg) {
    header("Location: inscriptions.php");
    exit();
}

// Parser les données
$inscription = [
    'date' => date('d/m/Y à H:i', strtotime($msg['date_envoi'])),
    'nom' => '',
    'prenom' => '',
    'classe' => '',
    'adresse' => '',
    'telephone' => '',
    'email' => ''
];

if (preg_match('/Enfant : (.+?) (.+)/', $msg['message'], $m)) {
    $inscription['nom'] = trim($m[1]);
    $inscription['prenom'] = trim($m[2]);
}
if (preg_match('/Classe : (.+)/', $msg['message'], $m)) {
    $inscription['classe'] = trim($m[1]);
}
if (preg_match('/Adresse : (.+)/', $msg['message'], $m)) {
    $inscription['adresse'] = trim($m[1]);
}
if (preg_match('/Téléphone : (.+)/', $msg['message'], $m)) {
    $inscription['telephone'] = trim($m[1]);
}
if (preg_match('/Email parent : (.+)/', $msg['message'], $m)) {
    $inscription['email'] = trim($m[1]);
}

// Charger TCPDF
require_once '../vendor/autoload.php';

// Créer le PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Métadonnées
$pdf->SetCreator('Association Aujourd\'hui vers Demain');
$pdf->SetAuthor('Aujourd\'hui vers Demain');
$pdf->SetTitle('Reçu d\'inscription - Aide aux devoirs');
$pdf->SetSubject('Reçu inscription');

// Supprimer header/footer par défaut
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Marges
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);

// Ajouter une page
$pdf->AddPage();

// ========== CONTENU DU REÇU ==========

// Logo (utilisation d'un placeholder ou URL)
$logoUrl = 'https://cdn-icons-png.flaticon.com/512/2904/2904869.png';
$pdf->Image($logoUrl, 85, 15, 40, 40, 'PNG', '', '', false, 300, '', false, false, 0, false, false, false);

// Titre de l'association
$pdf->SetY(60);
$pdf->SetFont('helvetica', 'B', 22);
$pdf->SetTextColor(102, 126, 234); // #667eea
$pdf->Cell(0, 12, 'Aujourd\'hui vers Demain', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 6, 'Association loi 1901 - Pantin', 0, 1, 'C');

// Trait décoratif
$pdf->SetY(85);
$pdf->SetDrawColor(102, 126, 234);
$pdf->SetLineWidth(1);
$pdf->Line(60, 85, 150, 85);

// Titre du document
$pdf->SetY(95);
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(0, 12, 'REÇU D\'INSCRIPTION', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 8, 'Aide aux devoirs - Année scolaire ' . date('Y') . '/' . (date('Y') + 1), 0, 1, 'C');

// Numéro de reçu
$pdf->SetY(120);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor(102, 126, 234);
$pdf->Cell(0, 6, 'N° ' . str_pad($id, 6, '0', STR_PAD_LEFT), 0, 1, 'C');

// Cadre informations
$pdf->SetY(135);
$pdf->SetFillColor(248, 249, 250);
$pdf->SetDrawColor(200, 200, 200);
$pdf->RoundedRect(25, 135, 160, 80, 5, '1111', 'DF');

// Informations de l'enfant
$pdf->SetY(142);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);

$pdf->SetX(35);
$pdf->Cell(50, 8, 'Enfant inscrit :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['prenom'] . ' ' . $inscription['nom'], 0, 1);

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(50, 8, 'Classe :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['classe'], 0, 1);

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(50, 8, 'Adresse :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['adresse'], 0, 1);

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(50, 8, 'Téléphone :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['telephone'], 0, 1);

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(50, 8, 'Email :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['email'], 0, 1);

$pdf->SetX(35);
$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(50, 8, 'Date inscription :', 0, 0);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 8, $inscription['date'], 0, 1);

// Horaires rappel
$pdf->SetY(215);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(17, 153, 142); // Vert
$pdf->Cell(0, 8, 'Rappel des horaires', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(0, 6, 'Lundi, Mardi, Jeudi, Vendredi de 16h30 à 18h00', 0, 1, 'C');
$pdf->Cell(0, 6, 'Niveaux : CP au CM2', 0, 1, 'C');

// Contact
$pdf->SetY(245);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(0, 5, 'Pour toute question, contactez-nous :', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 5, 'association@aujourdhui-vers-demain.fr', 0, 1, 'C');

// Date de génération
$pdf->SetY(265);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, 'Document généré le ' . date('d/m/Y à H:i'), 0, 1, 'C');

// ========== TÉLÉCHARGEMENT ==========
$filename = 'recu_inscription_' . $inscription['prenom'] . '_' . $inscription['nom'] . '_' . date('Y-m-d') . '.pdf';
$filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);

$pdf->Output($filename, 'D');
exit();
