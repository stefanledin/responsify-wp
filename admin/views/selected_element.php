<a name="markup-pattern"></a>
<div class="postbox">
    <div class="inside">
        <?php 
        $selected_element = get_option( 'selected_element', 'img' );
        ?>
        <h3><?php _e('Markup pattern', 'rwp'); ?></h3>
        <p>
            <?php _e('By default, RWP will create <b>img</b> tags with the <b>srcset</b> and <b>sizes</b> attributes using <a href="http://scottjehl.github.io/picturefill/">Picturefill 2.3.1</a>.', 'rwp'); ?>
        	<br>
        	<?php _e('You can also use <a href="https://github.com/scottjehl/picturefill/blob/1.2.1/README.md">Picturefill 1.2.1</a> and the old markup pattern that uses <b>span</b> element. That has some limitations though.', 'rwp') ?>
        </p>
        <div class="input-group">
            <table class="form-table select-markup-pattern">
                <tbody>
                    <tr>
                        <th>
                            <label>
                                <?php _e('img (Default)', 'rwp'); ?>
                                <input name="selected_element" type="radio" value="img" <?php echo ( $selected_element == 'img' ) ? 'checked="checked"' : ''; ?>>
                            </label>
                        </th>
                        <td>
                            <code>
                                &lt;img sizes=&quot;(min-width: 300px) 1024px, (min-width: 150px) 300px, 150px&quot; srcset=&quot;thumbnail.jpg 150w, medium.jpg 300w, large.jpg 1024w&quot;&gt;
                            </code>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label>
                                <?php _e('picture', 'rwp'); ?>
                                <input name="selected_element" class="js-has-message" data-message="picture-snippet" type="radio" value="picture" <?php echo ( $selected_element == 'picture' ) ? 'checked="checked"' : ''; ?>>
                            </label>
                        </th>
                        <td>
                            <code>
                                &lt;picture&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;source srcset=&quot;large.jpg&quot; media=&quot;(min-width: 300px)&quot;&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;source srcset=&quot;medium.jpg&quot; media=&quot;(min-width: 150px)&quot;&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;img srcset=&quot;thumbnail.jpg&quot;&gt;<br>
                                &lt;/picture&gt;
                            </code>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label>
                                <?php _e('span', 'rwp'); ?>
                                <input name="selected_element" type="radio" value="span" <?php echo ( $selected_element == 'span' ) ? 'checked="checked"' : ''; ?>>
                            </label>
                        </th>
                        <td>
                            <code>
                                &lt;span data-picture&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;span data-src=&quot;thumbnail.jpg&quot;&gt;&lt;/span&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;span data-src=&quot;medium.jpg&quot; data-media=&quot;(min-width: 150px)&quot;&gt;&lt;/span&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;span data-src=&quot;large.jpg&quot; data-media=&quot;(min-width: 300px)&quot;&gt;&lt;/span&gt;<br>
                                <br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;noscript&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=&quot;thumbnail.jpg&quot;&gt;<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;&lt;/noscript&gt;<br>
                                &lt;/span&gt;
                            </code>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="option-message" id="picture-snippet" <?php echo ( $selected_element == 'picture' ) ? 'style="display: block;"' : ''; ?>>
        	<?php _e('Notice that this snippet is required for the <b>picture</b> element to work in all browsers:') ?>
        	<br><br>
        	<code>
        		&ltscript&gt
        		<br>
        		    &nbsp;&nbsp;&nbsp;&nbsp;document.createElement( "picture" );
        		    <br>
        		&lt/script&gt
        	</code>
        	<br><br>
        	<small><?php _e('Note that if you are already including a recent version of the HTML5 Shiv (sometimes packaged with Modernizr), you may not need this line as it is included there as well.', 'rwp'); ?></small>
        </div>
        <?php submit_button( 'Save' ); ?>
    </div>
</div>