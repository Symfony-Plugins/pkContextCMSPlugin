// WORK IN PROGRESS. The problem is that sfJqueryReloadedPlugin uses
// plain old onSubmit, which submit() can't see and invoke. It does that
// because it has to emit just part of a form tag so it's not really in
// a position to call bind(). Think about a better way of handling
// this. A simple jquery wrapper call to set up the AJAXness of a 
// particular form that is already in the page using bind() so that
// logic like this can work.
//
// Also: create checkboxes with the same name as any named submit buttons
// and set them from the link so we can have the "distinguish commit
// buttons in the submission" behavior of "real" forms.

// Copyright 2009 P'unk Avenue, http://www.punkave.com/

// Transforms form submit buttons into JavaScript submit links (which are more
// easily styled). The links get the same classes as the button, which
// is hidden but sticks around to receive click events via trigger(). 
// The link also gets the id buttonid-link.
//
// By default the links don't have ids (just use classes to style them).
// If EVERY button will have an id, then you can include a _BUTTONID_
// placeholder in your template option:
//
// pkSubmitButtonAll("<a id='_BUTTONID_-link' href='#'>_LABEL_</a>")
// 
// Since not all buttons necessarily have IDs, the default template doesn't
// try to create one for the links.

function pkSubmitButtonAll()
{
  var options = { };
  if (arguments.length == 1)
  {
    options = arguments[0];
  }
  $(document).ready(
    function() {
      pkSubmitButton('body', options )
    }
  );
}

function pkSubmitButton(target)
{
  var options = { };
  if (arguments.length == 2)
  {
    options = arguments[1];
  }
  matches = $(target + ' input[type=submit]');
  matches.each(
    function(i) {
      if ($(this).data("pkSubmitAlready"))
      {
        return;
      }
      $(this).data("pkSubmitAlready", 1);
      if (!options['template'])
      {
        options['template'] = "<a href='#'>_LABEL_</a>";
      }
      link = options['template'].replace(
        "_LABEL_", pkHtmlEscape($(this).val()));
      var id = $(this).attr('id');
      if (id)
      {
        link = link.replace(
          "_BUTTONID_", id);
      }
      link = $(link);
      $(link).addClass(this.class);
      // Just stashing 'this' in 'button' here isn't good enough,
      // you wind up with the last value of button no matter which
      // item is clicked, we must use matches[i] explicitly
      $(link).click(
        function() {
          var form;
          var e = matches[i];
          while (e)
          {
            if (e.nodeName == 'FORM')
            {
              $(e).submit();
              break;
            }
            e = e.parentNode;
          }
          return false;
        }
      );
      $(this).after(link);
      $(this).hide();
      function pkHtmlEscape(html)
      {
        html = html.replace('&', '&amp;'); 
        html = html.replace('<', '&lt;'); 
        html = html.replace('>', '&gt;'); 
        html = html.replace('"', '&quot;'); 
        return html;
      }  
    }
  );
}
