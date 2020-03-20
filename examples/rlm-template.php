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
