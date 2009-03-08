<?php

// Subclass this to create your own slot classes that have
// custom renderers for either editing or normal viewing
// and/or custom storage. Set the 'class' key of the options 
// array you pass to pk_context_cms_slot to the class name.

class pkContextCMSNormalSlot
{
  public $slot;
  public $options;
  public $value;
  public __construct(pkContextCMSSlot $slot, $options, $object)
  {
    // Remember, the slot can be null if it hasn't been created yet!
    $this->slot = $slot;
    $this->options = $options;
    $this->value = $this->slot ? $this->slot->getValue() : "";
    // Your subclass constructor is a good place to use
    // $slot->id to look things up in foreign tables
  }

  // Here our data is in $this->value. You can use that technique
  // in your own slot subclasses, possibly in combination with
  // serialize() and unserialize(), or you can use a related table
  // which keeps the slot ID as a foreign key. 
  //
  // The latter is more powerful but requires extra database queries
  // per page. TODO: provide a facility that prepares outer joins
  // on additional registered tables used by custom slots so that
  // this is not necessary.
  //
  // REMEMBER: pkContextCMS implements version control, so you should
  // create a NEW instance of any related table on EVERY SAVE, using
  // the new slot's ID as a foreign key (see afterSave() below).

  public function renderEditView()
  {
    $options = $this->options;
    $type = $options['type'];
    $controlOptions = array();
    if ($type == 'rich')
    {
      $controlOptions['rich'] = 'FCK';
      $helper = 'textarea_tag';
      // You can set the toolbar, huzzah
      if (isset($options['tool']))
      {
        $controlOptions['tool'] = $options['tool'];
      }
    } 
    elseif ($type == 'multiline-text')
    {
      $helper = 'textarea_tag';
    }
    elseif ($type == 'text')
    {
      $helper = 'input_tag';
      if (isset($editor['maxlength']))
      {
        $controlOptions['maxlength'] = $options['maxlength'];
      }
    }
    elseif ($type == 'html')
    {
      $helper = 'textarea_tag';
    }
    else
    {
      throw new sfException("Unknown slot type: $type\n");
    }
    echo $helper("value_$name",
      $this->value,
      $controlOptions);
    $user = sfContext::getInstance()->getUser();
  }
  public function renderNormalView()
  {
    echo($this->value);
  }
  // Filters $value, which comes from the 'value' request parameter. 
  // The value you return is saved in the new slot's value field. If you
  // have more complex storage needs see afterSave().
  public function filter($value)
  {
    $type = $this->options['type'];
    if (($type == 'rich') || ($type == 'html'))
    {
      // A few servers are set up to parse their own output for
      // additional commands. So deactivate any PHP, etc. and SSI
      // directives to be on the safe side
      $value = str_replace(array("<?", "<!--#"), "", $value);
      // TODO: size limits, appropriate HTML tags limits, etc.
      // Implementing this well will require dealing with a lot
      // of different output from different browser rich text editors
    }
    elseif ($type == 'multiline-text')
    {
      $value = htmlentities($value);
      $value = preg_replace("/(\r\n|\r|\n)/", $value, '<br/>\n');
    }
    elseif ($type == 'text')
    {
      $value = htmlentities($value);
    }
    else
    {
      throw new sfException("Unknown slot type: $type\n");
    }
    // The value we return here gets saved in the slot's
    // value field. If you want to store additional information
    // in a related table, do that now. Set $this->slot->foreign_id
    // to the ID of the item in the related table so that you
    // can retrieve it later in your rendering methods. This field
    // will also be saved for you.
    //
    // Tip: a related table isn't the only way to go. You can also
    // use serialize() and unserialize() to store fancy data
    // structures in $value. I prefer related tables but they do
    // generate extra queries.
    return $value;
  }
  public function afterSave($slot)
  {
    // Everything we care about has already been saved in 
    // $slot->value. If your needs are more complex, this is
    // the place to create a new instance of another class and
    // store $slot->id as a unique foreign key so that you can
    // look up your object again.
  }
}
