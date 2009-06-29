function pkContextCMSConstructor() 
{
  this.onSubmitHandlers = new Object();
  this.registerOnSubmit = function (slotId, callback) 
  {
    if (!this.onSubmitHandlers[slotId])
    {
      this.onSubmitHandlers[slotId] = [ callback ];
      return;
    }
    this.onSubmitHandlers[slotId].push(callback);
  };
  this.callOnSubmit = function (slotId)
  {
    handlers = this.onSubmitHandlers[slotId];
    if (!handlers)
    {
      return;
    }
    for (i = 0; (i < handlers.length); i++)
    {
      handlers[i](slotId);
    }
  }
}

pkContextCMS = new pkContextCMSConstructor();


/* This handles search inputs sidewide */
$(document).ready(function() {
	
	if ($('input.pk-search-field').attr('value') == '') {
		$('input.pk-search-field').attr('value' , 'Search');
	}
		
	$('input.pk-search-field').focus(function(){
		if ($(this).attr('value') == 'Search') {
			$(this).attr('value' , '');
		}
	});

	$('input.pk-search-field').blur(function(){
		if ($(this).attr('value') == '') {
			$(this).attr('value' , 'Search');
		}
	});
});
