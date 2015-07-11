<?php
echo '<pre>';
    print_r(get_option('rwp_custom_media_queries'));
echo '</pre>';
?>
<script type="text/javascript">
    window.rwp = window.rwp || {};
    rwp.customMediaQueries = <?php echo json_encode(get_option('rwp_custom_media_queries'));?>;
</script>
<h3>Custom media queries</h3>
<table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th>Settings</th>
        </tr>
    </thead>
    <tbody class="rwp-custom-media-queries"></tbody>
</table>

<div class="rwp-add-setting">
    <p><button class="button">Add setting</button></
</div>

