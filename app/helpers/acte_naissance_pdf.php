<?php
require_once('tcpdf/tcpdf.php');
require_once('ActeNaissance.php');

setlocale(LC_TIME, 'fr_FR.UTF-8'); // Pour avoir les mois en français

$data = [
    'numeroRegistre' => '3456 DU 17/10/2000',
    'anneeRegistre' => '2000',
    'nom' => 'YAO',
    'prenoms' => 'Patrick Xavier-Anicet',
    'dateNaissance' => '2000-10-05',
    'heureNaissance' => '10:26',
    'lieuNaissance' => 'la Maternité de Koko /.',
    'nomPere' => 'KOUADIO Yao',
    'professionPere' => 'Instituteur',
    'nomMere' => 'IRIE Lou Génévié Antoinette',
    'professionMere' => 'Sans',
    'mentionMariage' => '... Néant ...',
    'mentionDivorce' => '... Néant ...',
    'mentionDeces' => '... Néant ...',
    'commune' => 'BOUAKE',
    'dateDelivrance' => '2018-10-10',
    'officierEtatCivil' => 'S. KOUASSI',
];

$acte = new ActeNaissance($data);

// Génération PDF
$pdf = new TCPDF();
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

$html = <<<HTML
<style>
    .center { text-align: center; }
    .mentions { border-top: 1px solid #000; margin-top: 20px; padding-top: 10px; }
</style>

<table width="100%">
<tr>
    <td width="50%">
        <strong>DÉPARTEMENT DE {$acte->commune}<br>COMMUNE DE {$acte->commune}</strong>
    </td>
    <td width="50%" align="right">
        <strong>REPUBLIQUE DE CÔTE D'IVOIRE</strong><br>
        <u>EXTRAIT</u><br>
        Du registre des actes de l'État Civil<br>
        Pour l'année {$acte->anneeRegistre}
    </td>
</tr>
</table>

<p class="center" style="margin-top:15px;"><strong>ETAT CIVIL<br>Centre principal Koko</strong></p>

<p><strong>N° {$acte->numeroRegistre}</strong></p>
<p><u>NAISSANCE DE</u></p>
<p><strong>{$acte->nom} {$acte->prenoms}</strong></p>

<p>
Le {$acte->getDateNaissanceFormatee()}<br>
à {$acte->heureNaissance} heures vingt six minutes<br>
né {$acte->nom} {$acte->prenoms}<br>
à {$acte->lieuNaissance}<br><br>

Fils de {$acte->nomPere} /.<br>
Profession : {$acte->professionPere} /.<br><br>
et de {$acte->nomMere} /.<br>
Profession : {$acte->professionMere} /.
</p>

<div class="mentions">
<p><strong>MENTIONS (éventuellement)</strong><br>
Marié le ... {$acte->mentionMariage}<br>
Divorcé le ... {$acte->mentionDivorce}<br>
Décédé le ... {$acte->mentionDeces}</p>
</div>

<p style="margin-top:20px;">
Délivré à {$acte->commune}, le {$acte->getDateDelivranceFormatee()}<br><br>
L'Officier d'État Civil<br>
<strong>{$acte->officierEtatCivil}</strong>
</p>
HTML;

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("acte_naissance.pdf", "I");
