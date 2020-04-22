<?php
$aww_options = get_option( 'aww_options' );
$aww_roles = isset( $aww_options['roles'] ) ? $aww_options['roles'] : array();
?>
<style>
.aco-toggle {
    position: relative;
    display:inline-block;
    user-select: none;
}

.aco-toggle__items {
    box-sizing: border-box;
}

.aco-toggle__items > * {
    box-sizing: inherit;
}

.aco-toggle__input[type=checkbox] {
    border-radius: 2px;
    border: 2px solid #6c7781;
    margin-right: 12px;
    transition: none;
    height: 100%;
    left: 0;
    top: 0;
    margin: 0;
    padding: 0;
    opacity: 0;
    position: absolute;
    width: 100%;
    z-index: 1;
}

.aco-toggle__track {
    background-color: #fff;
    border: 2px solid #6c7781;
    border-radius: 9px;
    display: inline-block;
    height: 18px;
    width: 36px;
    vertical-align: top;
    transition: background .2s ease;
}

.aco-toggle__thumb {
    background-color: #6c7781;
    border: 5px solid #6c7781;
    border-radius: 50%;
    display: block;
    height: 10px;
    width: 10px;
    position: absolute;
    left: 4px;
    top: 4px;
    transition: transform .2s ease;
}

.aco-toggle__off {
    position: absolute;
    right: 6px;
    top: 6px;
    color: #6c7781;
    fill: currentColor;
}

.aco-toggle__on {
    position: absolute;
    top: 6px;
    left: 8px;
    border: 1px solid #fff;
    outline: 1px solid transparent;
    outline-offset: -1px;
    display: none;
}

.aco-toggle__input[type=checkbox]:checked + .aco-toggle__items .aco-toggle__track {
    background-color: #11a0d2;
    border: 9px solid transparent;
}

.aco-toggle__input[type=checkbox]:checked + .aco-toggle__items .aco-toggle__thumb {
    background-color: #fff;
    border-width: 0;
    transform: translateX(18px);
}

.aco-toggle__input[type=checkbox]:checked + .aco-toggle__items .aco-toggle__off {
    display: none;
}

.aco-toggle__input[type=checkbox]:checked + .aco-toggle__items .aco-toggle__on {
    display: inline-block;
}

.scpo-reset-response {
    margin-left:15px;
    color:#0085ba;
}
</style>
<div class="wrap">
    <div id="icon-tools" class="icon32"><br/></div>
    <h1><?php esc_html_e( get_admin_page_title(), AWW_TEXTDOMAIN ); ?></h1>
    <p><span style="font-weight:bold;"><?php esc_html_e( 'This plugin disables the Admin Bar from the frontend only, it does not affect the dashboard', AWW_TEXTDOMAIN ); ?></span></p>
    <?php if ( isset( $_GET['msg'] ) ) : ?>
        <div id="message" class="updated below-h2">
            <?php if ( $_GET['msg'] == 'update' ) : ?>
                <p><?php _e( 'Settings Updated.', AWW_TEXTDOMAIN ); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'aww_nonce' ); ?>

        <div id="aco_select_objects">

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Disable admin bar for Role', AWW_TEXTDOMAIN ) ?></th>
                        <td>
                            <label>
                                <div class="aco-toggle">
                                    <input id="aco_selectall_objects" class="aco-toggle__input" type="checkbox">
                                    <div class="aco-toggle__items">
                                        <span class="aco-toggle__track"></span>
                                        <span class="aco-toggle__thumb"></span>
                                        <svg class="aco-toggle__off" width="6" height="6" aria-hidden="true"
                                             role="img" focusable="false" viewBox="0 0 6 6">
                                            <path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
                                        </svg>
                                        <svg class="aco-toggle__on" width="2" height="6" aria-hidden="true"
                                             role="img" focusable="false" viewBox="0 0 2 6">
                                            <path d="M0 0h2v6H0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                &nbsp;<?php _e( 'Select All', AWW_TEXTDOMAIN ) ?></label><br>
                            <?php
                            
                            global $wp_roles;

                            if ( ! isset( $wp_roles ) ) {
                                $wp_roles = new WP_Roles();
                            }

                            $roles = $wp_roles->get_names();

                            foreach ( $roles as $role => $name ) {
                                ?>
                                <label>
                                    <div class="aco-toggle">
                                        <input class="aco-toggle__input" type="checkbox"
                                               name="roles[]" value="<?php echo $role; ?>" <?php
                                        if ( isset( $aww_roles ) && is_array( $aww_roles ) ) {

	                                        if ( in_array( $role, $aww_roles ) ) {
		                                        echo 'checked="checked"';
                                            }
                                            
                                        }
		                                ?>>
                                        <div class="aco-toggle__items">
                                            <span class="aco-toggle__track"></span>
                                            <span class="aco-toggle__thumb"></span>
                                            <svg class="aco-toggle__off" width="6" height="6" aria-hidden="true"
                                                 role="img" focusable="false" viewBox="0 0 6 6">
                                                <path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
                                            </svg>
                                            <svg class="aco-toggle__on" width="2" height="6" aria-hidden="true"
                                                 role="img" focusable="false" viewBox="0 0 2 6">
                                                <path d="M0 0h2v6H0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    &nbsp;<?php echo esc_html( $name ); ?></label><br>
                                    <?php
                                }
                                ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" class="button-primary" name="aww_submit" value="<?php _e( 'Update',  AWW_TEXTDOMAIN ); ?>">
        </p>
    </form>
</div>

<script>

    (function ($) {

        $( "#aco_selectall_objects" ).on( 'click', function () {
            var items = $( "#aco_select_objects input");
            if ( $(this).is( ':checked' ) )
                $( items ).prop( 'checked', true );
            else
                $( items ).prop( 'checked', false );
        });

    })(jQuery)

</script>