// Copyright 2009 P'unk Avenue, http://www.punkave.com/

function pkMultipleSelectAll()
{
  $(document).ready(
    function() {
      pkMultipleSelect('body', { } )
    }
  );
}

function pkMultipleSelect(target, options)
{
  $(target + ' select[multiple]').each(
    function(i) {
      var name = $(this).attr('name');
      var id = $(this).attr('id');
      var values = [];
      var labels = [];
      var selected = [];
      var j;
      for (j = 0; (j < this.options.length); j++)
      {
        var option = this.options[j];
        values.push(option.value);
        labels.push(option.innerHTML);
        // Firefox is a little cranky about this,
        // try it both ways
        selected.push(option.getAttribute('selected') || option.selected);
      }
      if (id === '')
      {
        // Hopefully unique
        id = name;
      }
      var html = "<div class='pk-multiple-select' id='" + id + "'>";
      html += "<select name='select-" + name + "'>";
      html += "</select>\n";
      for (j = 0; (j < values.length); j++)
      {
        html += "<input type='checkbox' name='" + name + "'";
        if (selected[j])
        {
          html += " checked";
        }
        html += " value=\"" + pkHtmlEscape(values[j]) + 
          "\" style='display: none'/>";
      }
      html += "<ul class='pk-multiple-select-list'>";
      if (!options['remove'])
      {
        options['remove'] = ' <span>Remove</span>';
      }
      for (j = 0; (j < values.length); j++)
      {
        html += "<li style='display: none'><a href='#' title='Remove this item' class='pk-multiple-select-remove'>" + 
          pkHtmlEscape(labels[j]) + 
          options['remove'] + "</a></li>\n";
      }
      html += "</ul>\n";
      // Handy for clearing floats
      html += "<div class='pk-multiple-select-after'></div>\n";
      html += "</div>\n";
      $(this).replaceWith(html);
      var select = $("#" + id + " select");
      var k;
      var items = $('#' + id + ' ul li');
      for (k = 0; (k < values.length); k++)
      {
        $(items[k]).data("boxid", values[k]);
        $(items[k]).click(function() { update($(this).data("boxid")); return false; });
      }
      function update(remove)
      {
        var ul = $("#" + id + " ul");
        var select = $("#" + id + " select")[0];
        var index = select.selectedIndex;
        var value = false;
        if (index > 0)
        {
          value = select.options[index].value;
        }
        var boxes = $('#' + id + " input[type=checkbox]");
        boxes[0].checked = false;
        for (k = 1; (k < values.length); k++)
        {
          if (boxes[k].value === remove)
          {
            boxes[k].checked = false;
          }
          if (boxes[k].value === value)
          {
            boxes[k].checked = true;
          }
        }
        var items = $('#' + id + ' ul li');
        var k;
        var html;
        for (k = 0; (k < values.length); k++)
        {
          if (boxes[k].checked)
          {
            $(items[k]).show();
          }
          else
          {
            $(items[k]).hide();
            html += "<option ";
            if (k == 0)
            {
              // First option is "pick one" message
              html += " selected ";
            }
            html += "value=\"" + pkHtmlEscape(values[k]) + "\">" +
              pkHtmlEscape(labels[k]) + "</option>";
          }
        }
        // Necessary in IE
        $(select).replaceWith("<select name='select-" + name + "'>" + html + "</select>");
        $("#" + id + " select").change(function() { update(false); });
      }
      function pkHtmlEscape(html)
      {
        html = html.replace('&', '&amp;'); 
        html = html.replace('<', '&lt;'); 
        html = html.replace('>', '&gt;'); 
        html = html.replace('"', '&quot;'); 
        return html;
      }  
      update(false);
    }
  );
}
