<?php

class Devllo_Events_Bookings_Checkout_Fields {


    public function __construct(){
        add_shortcode ('devllo_events_fields', array($this, 'devllo_events_fields'));
        add_action('devllo_events_bookings_before_checkout_form', array($this, 'devllo_events_bookings_fields'));



    }

    public function devllo_events_fields( $atts = '', $content = null ) {
    if (!is_admin()){

        ob_start();

        $value = shortcode_atts( array(
            'meta_key' => '',
            'field_type' => '',
            'label' => '', // custom field label
            'size' => '', // input size
            'class' => '', // custom class
            'required' => '', // make this field required
            'options'=> array(
        ),         
        ), $atts );

        // Create Select Fields
        if ($value['field_type'] == 'select'){
            if (!empty($value['options'])){
                // $options = $value['options'];
                    parse_str( str_replace(",", "&", $value['options']), $options);
                    ?>
                    <label for="<?php echo $value['meta_key']; ?>"> <?php echo $value['label']; ?></label><br/>
                    <select name="<?php echo $value['meta_key'];?>" id="<?php echo $value['meta_key']; ?>" form="<?php echo $value['meta_key']; ?>">
                            <option value=""></option>
                    <?php
                // print_r($options);
                                foreach($options as $option => $option_value)
                                {
                                ?>
                            <option value="<?php echo $option; ?>"><?php echo $option_value; ?></option>
                            <?php } ?>
                    </select>
                        <br/>
                    <?php
            }
            
        }

        // Create Text Fields
        elseif ($value['field_type'] == 'text')
        {
            return $value['label'] . ': <input type="'. $value['field_type'] . '" name="'.$value['meta_key'] . '" class="'.$value['meta_key'] . '" /><br/>';

        }

        // Create TextArea Fields
        elseif ($value['field_type'] == 'textarea')
        {
            return $value['label'] . ': <textarea name="'.$value['meta_key'] . '" class="'.$value['meta_key'] . '" /></textarea><br/>';

        }
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
    }

}

public function devllo_add_event_checkout_fields(){
echo do_shortcode( '[devllo_events_fields meta_key="quote" field_type="select" label="Quote" options="key1=value 1, key2=value 2"]' );

echo do_shortcode( '[devllo_events_fields meta_key="quo2te" field_type="text" label="Quo2te"]' );

echo do_shortcode( '[devllo_events_fields meta_key="quo2tearea" field_type="textarea" label="Text Area"]' );
}

// add_action('devllo_events_bookings_before_checkout_form', 'devllo_add_event_checkout_fields');

function devllo_events_bookings_fields(){
    global $post;

    $postID = $post->ID;

    $event_price = get_post_meta( $post->ID, 'devllo_event_price_key', true );
    // If event is paid, load checkout page

    if (isset($event_price) && $event_price > 0){
    } else {
        $this->devllo_add_event_checkout_fields();


    }
}

}

new Devllo_Events_Bookings_Checkout_Fields();
