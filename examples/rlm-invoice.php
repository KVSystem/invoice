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

include(__DIR__ .'/rlm-template.php');
?>
