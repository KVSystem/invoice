<?php

require __DIR__ . '/../vendor/autoload.php';

use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Positions\PeriodPosition;
use Proengeno\Invoice\Positions\YearlyBasePosition;
use Proengeno\Invoice\Positions\YearlyQuantityBasePosition;

/** Invoice erstellen **/
$invoice = new Invoice(
    new PositionGroup(PositionGroup::NET, 19.0, [
        new YearlyQuantityBasePosition('Leistung', 566, 12.53, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Grundpreis', 440 , new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new PeriodPosition('Wirkarbeit', 0.002862, 213984, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Grundpreis', 440, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new PeriodPosition('Konzessionsabgabe', 0.0003, 213984, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Entgelt für Einbau, Betrieb und Wartung der Messtechnik', 471.4, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Entgelt für Einbau, Betrieb und Wartung der Messtechnik', 129.5, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Entgelt für Einbau, Betrieb und Wartung der Messtechnik', 284.7, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new PeriodPosition('Gassteuer', 0.0055, 213984, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new PeriodPosition('Bilanzierungsumlage', 0.0055, 213984, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new PeriodPosition('Arbeitspreis Versorger', 0.0233, 213984, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
        new YearlyBasePosition('Entgelt für Einbau, Betrieb und Wartung der Messtechnik', 118.8, new \DateTime('2019-12-01'), new \DateTime('2019-12-31')),
    ])
);

/** Invoice Formatierung setzten **/
$invoice->setFormatter(
    new Formatter('de_DE', [
        'Arbeitspreis Versorger' => ['quantity:pattern' => "#,##0 kWh", 'price:pattern' => "#,##0.000 Ct/kWh", 'price:multiplier' => 100],
        'Grundpreis Versorger' => ['price:pattern' => "#,##0.00 €/Jahr"],
        'Leistung' => ['quantity:pattern' => "#,##0.000 kW", 'price:pattern' => "#,##0.00 €/kW/Jahr"],
        'Grundpreis' => ['price:pattern' => "#,##0.00 €/Jahr"],
        'Wirkarbeit' => ['quantity:pattern' => "#,##0 kWh", 'price:pattern' => "#,##0.0000 Ct/kWh", 'price:multiplier' => 100],
        'Entgelt für Einbau, Betrieb und Wartung der Messtechnik' => ['price:pattern' => "#,##0.00 €/Jahr"],
        'Konzessionsabgabe' => ['quantity:pattern' => "#,##0 kWh", 'price:pattern' => "#,##0.0000 Ct/kWh", 'price:multiplier' => 100],
        'Gassteuer' => ['quantity:pattern' => "#,##0 kWh", 'price:pattern' => "#,##0.0000 Ct/kWh", 'price:multiplier' => 100],
        'Bilanzierungsumlage' => ['quantity:pattern' => "#,##0 kWh", 'price:pattern' => "#,##0.0000 Ct/kWh", 'price:multiplier' => 100],
    ])
);

/** Helfer zum strukturieren der Html-Struktur **/
$positionTypes = [
    'Lieferung' => ['Arbeitspreis Versorger', 'Grundpreis Versorger'],
    'Netznutzung' => ['Leistung', 'Grundpreis', 'Wirkarbeit', 'Entgelt für Einbau, Betrieb und Wartung der Messtechnik'],
    'Steuer und Abgaben' => ['Gassteuer', 'Bilanzierungsumlage', 'Konzessionsabgabe'],
];
?>

<table width="100%">
    <?php foreach ($positionTypes as $name => $type): ?>
        <tr>
            <th colspan='5'><h3><?php echo substr($name, 0, 30) ?></h3></th>
        </tr>
        <tr>
            <th>Bezeichung</th>
            <th>Zeiraum</th>
            <th align="right">Menge</th>
            <th align="right">Preis</th>
            <th align="right">Betrag</th>
        </tr>
        <?php $positions = $invoice->netPositions()->only($type) ?>
        <?php foreach ($positions->group() as $groupedPositions): ?>
            <?php foreach ($groupedPositions as $position): ?>
            <tr>
            <td style="white-space: nowrap;"><?php echo $position->amount() < 0 ? 'bereits berechnet' : substr($position->name(), 0, 45) ?></td>
            <td style="white-space: nowrap;"><?php echo  $position->format('from') ?> - <?php echo $position->format('until') ?></td>
                <td align="right" style="white-space: nowrap;">
                    <?php echo $position->format('quantity') ?>
                </td>
                <td align="right" style="white-space: nowrap;">
                    <?php echo $position->format('price') ?>
                </td>
                <td align="right" style="white-space: nowrap;">
                    <?php echo $position->format('amount') ?>
                </td>
            </tr>
            <?php endforeach ?>
            <tr>
                <td colspan="5"><hr></td>
            <tr>
        <?php endforeach ?>
        <tr>
            <td colspan="4">Teilsumme</td>
            <td align="right"><?php echo $positions->format('sumAmount') ?></td>
        <tr>
    <?php endforeach ?>
    <tr>
        <td colspan="5"><hr></td>
    <tr>
    <tr>
        <td colspan="4">Gesamtsumme Netto</td>
        <td align="right"><b><?php echo $invoice->format('netAmount') ?></b></td>
    <tr>
    <tr>
        <td colspan="4">zuzüglich USt.</td>
        <td align="right"><b><?php echo $invoice->format('vatAmount') ?></b></td>
    <tr>
    <tr>
        <td colspan="4">Gesamtsumme Brutto</td>
        <td align="right"><b><?php echo $invoice->format('grossAmount') ?></b></td>
    <tr>
</table>
