// Copyright 2009 P'unk Avenue, http://www.punkave.com/

// Transforms select elements matching the specified selector.  
// You won't want to do this to every select element in your form,
// so give them a class like .pkRadioSelect and use a class selector like 
// .pkRadioSelect (but not .pkRadioSelectContainer, which we use for
// the span that encloses our toggle buttons). Make sure your selector is 
// specific enough not to match other elements as well.
//
// We set the pkRadioOptionSelected class on the currently selected link
// element.

function pkRadioSelect(target, options)
{
  $(target).each(
    function(i) {
      $(this).hide();
      var html = "";
      var links = "";
      var j;
			var total = this.options.length;
      linkTemplate = getOption("linkTemplate",
        "<a href='#'>_LABEL_</a>");
      spanTemplate = getOption("spanTemplate",
        "<span class='pk-radio-select-container'>_LINKS_</span>");
      betweenLinks = getOption("betweenLinks", " ");
      for (j = 0; (j < this.options.length); j++)
      {
        if (j > 0)
        {
          links += betweenLinks;
        }
        links += 
          linkTemplate.replace("_LABEL_", $(this.options[j]).html());
      }
      span = $(spanTemplate.replace("_LINKS_", links));
      var select = this;
      links = span.find('a');
      $(links[select.selectedIndex]).addClass('pk-radio-option-selected');
      links.each(
        function (j)
        {
          $(this).data("pkIndex", j);
					$(this).addClass('option-'+j);
					
					if (j == 0)
					{
						$(this).addClass('first');
					}
					
					if (j == total-1)
					{
						$(this).addClass('last');						
					}
          $(this).click(
            function (e)
            {
              select.selectedIndex = $(this).data("pkIndex");
              parent = ($(this).parent());
              parent.find('a').removeClass('pk-radio-option-selected'); 
              $(this).addClass('pk-radio-option-selected'); 
              return false;
            }
          );
        }
      );
      $(this).after(span);
      function getOption(name, def)
      {
        if (name in options)
        {
          return options[name];
        }
        else
        {
          return def;
        }
      }
    }
  );
}
