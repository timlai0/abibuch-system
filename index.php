<?php

ini_set('memory_limit', '-1');
function seitennummer() {
	GLOBAL $pdf;


	$pdf->SetY(-15);
	$pdf->SetFont('Arial','',11);
	$pn = $pdf->PageNo();

	if($pn %2 != 0) {

		$pdf->Cell(0,5,'Seite '.$pn,0,0,'L');
	} else {
		$pdf->Cell(0,5,'Seite '.$pn,0,0,'R');
	}


}


require_once('fpdf/fpdf.php');
$pdf=new FPDF('P','mm','A4');

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 10);


$pdf->SetFont('Arial','B',15);
$pdf->SetY(20);


$source = file_get_contents("ex.xml");

$xml = simplexml_load_string($source);


$pdf->SetFont('Arial','',9);
$pdf->Write(5, utf8_decode($xml->vorwort->vorwort_text)); 
seitennummer();


foreach ($xml->schuelerschaft->schueler as $data) {


	$klasse = utf8_decode((string)$data->schueler_info->schueler_info_kurs->attributes()[1]);

	$pdf->AddPage();

	$pdf->SetFont('Arial','B',15);


	$pdf->Cell(70,7,utf8_decode((string)$data->schueler_info->schueler_info_vorname).' '.utf8_decode((string)$data->schueler_info->schueler_info_nachname),'TLB');


	$pdf->Cell(30,7,utf8_decode((string)$data->schueler_info->schueler_info_geburtsdatum), 1,0,'C');
	$pdf->Cell(0,7,$klasse, 1,1,'C');


	if (!empty($data->schueler_info->schueler_info_fotos)) {


		if (!empty($data->schueler_info->schueler_info_fotos->schueler_info_foto[0]->attributes()['path'])) {


			$path = (string)$data->schueler_info->schueler_info_fotos->schueler_info_foto[0]->attributes()['path'];
			$size = getimagesize($path);

			$pdf->Image($path,10,20, 0,50);		
		}

		if (!empty($data->schueler_info->schueler_info_fotos->schueler_info_foto[1]->attributes()['path'])) {


			$path = (string)$data->schueler_info->schueler_info_fotos->schueler_info_foto[1]->attributes()['path'];
			$size = getimagesize($path);

			$pdf->Image($path,100,20, 0,50);		
		}
		
	}


	$pdf->SetY(50 + 20);

	$pdf->SetFont('Arial','',8);


	if (!empty($data->schueler_steckbrief->schueler_steckbrief_antworten)) {
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(5,8,"Steckbrief",0,1,'L');
		$pdf->SetFont('Arial','',8);

		foreach ($data->schueler_steckbrief->schueler_steckbrief_antworten as $kom) {
			$pdf->Cell(70,4,utf8_decode((string)$kom->schueler_steckbrief_frage),'',0,'L');
			$pdf->MultiCell(0,4,utf8_decode((string)$kom->schueler_steckbrief_antwort),'','L');
		}


		$pdf->SetY($pdf->GetY() + 5);
	}



	if (!empty($data->schueler_kommentare->schueler_kommentar)) {
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(5,8,"Schülekommentare",0,1,'L');
		$pdf->SetFont('Arial','',8);

		foreach ($data->schueler_kommentare->schueler_kommentar as $kom) {
			$pdf->Cell(70,3,utf8_decode((string)$kom->schueler_kommentar_autor),0,0,'L');
			$pdf->MultiCell(0,3,utf8_decode((string)$kom->schueler_kommentar_text),0, 'L');
		}


		$pdf->SetY($pdf->GetY() + 5);
	}

	if (!empty($data->schueler_berufsvorschlaege->schueler_berufsvorschlag)) {
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(5,8,"Berufsvorschläge",0,1,'L');
		$pdf->SetFont('Arial','',8);

		foreach ($data->schueler_berufsvorschlaege->schueler_berufsvorschlag as $kom) {
			$pdf->Cell(70,3,utf8_decode((string)$kom->schueler_berufsvorschlag_verfasser),0,0,'L');
			$pdf->Cell(0,3,utf8_decode((string)$kom->schueler_berufsvorschlag_text),0,1,'L');
		}
	}



//seiten ende
	seitennummer();

}



//lehrer
$pdf->AddPage();


foreach ($xml->kollegium->lehrer as $data) {

	$pdf->SetFont('Arial','B',15);

	$pdf->Cell(0,7,utf8_decode($data->lehrer_vorname).' '.utf8_decode($data->lehrer_nachname),'1',1);
	$pdf->SetY($pdf->GetY() + 2);

	if (!empty($data->lehrer_kommentare)) {
		$pdf->SetFont('Arial','B',14);
		$pdf->SetFont('Arial','',8);

		foreach ($data->lehrer_kommentare->lehrer_kommentar_text as $kom) {
			$pdf->Cell(5,3,utf8_decode((string)$kom),0,1,'L');
		}
	}

	$pdf->SetY($pdf->GetY() + 5);


}

//Schuelerzitat
$pdf->AddPage();

$pdf->SetFont('Arial','',11);


foreach ($xml->zitate->zitate_schueler->zitat_schueler as $data) {
	$pdf->MultiCell(0,5,utf8_decode((string)$data),1,1);
	$pdf->SetY($pdf->GetY() + 1);

}



//lehrerzitate
$pdf->AddPage();

$pdf->SetFont('Arial','',11);


foreach ($xml->zitate->zitate_lehrer->zitat_lehrer as $data) {
	$pdf->MultiCell(0,5,utf8_decode((string)$data),1,1);
	$pdf->SetY($pdf->GetY() + 1);

}

//impressum
$pdf->AddPage();

$pdf->SetFont('Arial','',9);
$pdf->Write(5, utf8_decode($xml->impressum->impressum_text)); 
seitennummer();




$pdf->Output();
