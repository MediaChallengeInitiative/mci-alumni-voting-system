<?php
/**
 * Print Election Results as PDF
 * Uses TCPDF library for PDF generation
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include 'includes/session.php';

function generateRow($conn){
    $contents = '';

    // Get all positions ordered by priority
    $sql = "SELECT * FROM positions ORDER BY priority ASC";
    $query = $conn->query($sql);

    if (!$query) {
        error_log("Print PDF Error - Positions query failed: " . $conn->error);
        return '<tr><td colspan="2">Error loading positions</td></tr>';
    }

    while($row = $query->fetch_assoc()){
        $id = intval($row['id']);
        $contents .= '
            <tr>
                <td colspan="2" align="center" style="font-size:15px; background-color:#f0f0f0;"><b>'.htmlspecialchars($row['description']).'</b></td>
            </tr>
            <tr>
                <td width="80%"><b>Candidates</b></td>
                <td width="20%"><b>Votes</b></td>
            </tr>
        ';

        // Get candidates for this position using prepared statement
        $cstmt = $conn->prepare("SELECT * FROM candidates WHERE position_id = ? ORDER BY lastname ASC");
        $cstmt->bind_param("i", $id);
        $cstmt->execute();
        $cresult = $cstmt->get_result();

        while($crow = $cresult->fetch_assoc()){
            $candidateId = intval($crow['id']);

            // Count votes for this candidate using prepared statement
            $vstmt = $conn->prepare("SELECT COUNT(*) as vote_count FROM votes WHERE candidate_id = ?");
            $vstmt->bind_param("i", $candidateId);
            $vstmt->execute();
            $vresult = $vstmt->get_result();
            $vrow = $vresult->fetch_assoc();
            $votes = $vrow['vote_count'];
            $vstmt->close();

            $contents .= '
                <tr>
                    <td>'.htmlspecialchars($crow['lastname']).", ".htmlspecialchars($crow['firstname']).'</td>
                    <td align="center">'.$votes.'</td>
                </tr>
            ';
        }
        $cstmt->close();
    }

    return $contents;
}

// Get election title from config
$configFile = __DIR__ . '/config.ini';
if (!file_exists($configFile)) {
    die('Configuration file not found');
}

$parse = parse_ini_file($configFile, FALSE, INI_SCANNER_RAW);
if ($parse === false || !isset($parse['election_title'])) {
    die('Invalid configuration file');
}
$title = $parse['election_title'];

// Check if TCPDF exists
$tcpdfPath = __DIR__ . '/../tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    die('TCPDF library not found. Please install TCPDF.');
}

require_once($tcpdfPath);

try {
    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Result: '.$title);
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont('helvetica');
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    $content = '';
    $content .= '
        <h2 align="center">'.htmlspecialchars($title).'</h2>
        <h4 align="center">Tally Result</h4>
        <p align="center" style="font-size:10px; color:#666;">Generated: '.date('F j, Y - g:i A').'</p>
        <br>
        <table border="1" cellspacing="0" cellpadding="5" style="width:100%;">
    ';
    $content .= generateRow($conn);
    $content .= '</table>';
    $content .= '<br><p align="center" style="font-size:9px; color:#999;">&copy; '.date('Y').' Media Challenge Initiative</p>';

    $pdf->writeHTML($content);
    $pdf->Output('election_result.pdf', 'I');

} catch (Exception $e) {
    error_log("Print PDF Error: " . $e->getMessage());
    die('Error generating PDF: ' . $e->getMessage());
}
?>