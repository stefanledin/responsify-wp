<?php
$custom_media_queries = array(
    'default' => array(
        array(
            'image_size' => 'thumbnail'
        ),
        array(
            'image_size' => 'medium',
            'media_property' => 'min-width',
            'media_value' => '500px'
        ),
        array(
            'image_size' => 'large',
            'media_property' => 'min-width',
            'media_value' => '1024px'
        )
    ),
    'scenarios' => array(
        array(
            'type' => 'page',
            'operator' => '==',
            'value' => 'sample-page',
            'media_queries' => array(
                array(
                    'image_size' => 'medium'
                ),
                array(
                    'image_size' => 'large',
                    'media_property' => 'min-width',
                    'media_value' => '768px'
                )
            )
        ),
        array(
            'type' => 'template',
            'operator'
        )
    )
);
// update_option( 'rwp_custom_media_queries', $custom_media_queries);
?>
<h3>Custom media queries</h3>
<table class="wp-list-table widefat striped">
    <thead>
        <tr>
            <th>Settings</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody class="rwp-custom-media-queries">
        <tr>
            <td>
                <div class="rwp-custom-media-queries-settings-setting">
                    <p class="row-title">Default</p>
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th>Image size</th>
                                <th>Property</th>
                                <th>Value</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="js-media-queries-table">
                            <tr>
                                <td>
                                    <select name="image-size">
                                        <?php foreach ( get_intermediate_image_sizes() as $image_size ) : ?>
                                            <option><?php echo $image_size;?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="js-add-new-media-query">
                        <label>Add breakpoint
                            <select name="image-size">
                                <?php foreach ( get_intermediate_image_sizes() as $image_size ) : ?>
                                    <option><?php echo $image_size;?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="property">
                                <option>min-width</option>
                                <option>max-width</option>
                            </select>
                            <input type="text" name="breakpoint" placeholder="Breakpoint">
                            <button class="button">Add</button>
                        </label>
                    </div>
                </div>
            </td>
            <td>
                <button class="button">Close</button>
            </td>
        </tr>
        <tr>
            <td>
                <p><strong>When template is equal to wide</strong></p>
            </td>
            <td>
                <button class="button">Edit</button>
                <button class="button">Delete</button> 
            </td>
        </tr>
        <tr>
            <td>
                <p><strong>When original image has class wp-medium</strong></p>
            </td>
            <td>
                <button class="button">Edit</button>
                <button class="button">Delete</button> 
            </td>
        </tr>
    </tbody>
</table>

<div class="js-add-setting">
    <!-- <label>When
        <select>
            <option>Page</option>
            <option>Template</option>
            <option>Image</option>
        </select>
    </label>
    <select>
        <option>Is equal to</option>
        <option>Is not equal to</option>
    </select> -->
    <p><button class="button">Add setting</button></
</div>

