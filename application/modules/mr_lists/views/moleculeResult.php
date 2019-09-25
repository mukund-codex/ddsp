<table class="table table-striped table-condensed">
    <th>Molecule</th>
    <th>Brand Name</th>
    <th>Other Brand Name</th>
    <th>Rxn</th>
    <?php foreach($records as $record) : ?>
    <tr>
        <td><?php echo $record['molecule_name'] ?></td>        
        <td><?php echo $record['brand_name'] ?></td>
        <td><?php echo $record['custom_brand_name'] ?></td>
        <td><?php echo $record['rxn'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>