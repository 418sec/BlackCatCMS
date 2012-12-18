<?php
    include_once '../../../helper.inc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
  <link href="../../../frontend.css" rel="stylesheet" type="text/css"/>
  <link type="text/css" href="../../themes/base/jquery-ui.css" rel="stylesheet" />
 	<script type="text/javascript" src="../../../jquery-core/jquery-core.min.js"></script>
	<script type="text/javascript" src="../jquery.ui.core.min.js"></script>
	<?php echo _loadFile( '../presets/autocomplete.preset' ); ?>
	<script type="text/javascript" src="../jquery.ui.autocomplete.min.js"></script>
	<script type="text/javascript">
	(function($) {
		$.widget("ui.combobox", {
			_create: function() {
				var self = this;
				var select = this.element.hide();
				var input = $("<input>")
					.insertAfter(select)
					.autocomplete({
						source: function(request, response) {
							var matcher = new RegExp(request.term, "i");
							response(select.children("option").map(function() {
								var text = $(this).text();
								if (!request.term || matcher.test(text))
									return {
										id: $(this).val(),
										label: text.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + request.term.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi, "\\$1") + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>"),
										value: text
									};
							}));
						},
						delay: 0,
						select: function(e, ui) {
							if (!ui.item) {
								// remove invalid value, as it didn't match anything
								$(this).val("");
								return false;
							}
							$(this).focus();
							select.val(ui.item.id);
							self._trigger("selected", null, {
								item: select.find("[value='" + ui.item.id + "']")
							});
							
						},
						minLength: 0
					})
					.addClass("ui-widget ui-widget-content ui-corner-left");
				$("<button>&nbsp;</button>")
				.insertAfter(input)
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				}).removeClass("ui-corner-all")
				.addClass("ui-corner-right ui-button-icon")
				.position({
					my: "left center",
					at: "right center",
					of: input,
					offset: "-1 0"
				}).css("top", "")
				.click(function() {
					// close if already visible
					if (input.autocomplete("widget").is(":visible")) {
						input.autocomplete("close");
						return;
					}
					// pass empty string as value to search for, displaying all results
					input.autocomplete("search", "");
					input.focus();
				});
			}
		});

	})(jQuery);
		
	$(function() {
		$("select").combobox();
	});
	</script>
	<style>
		/* TODO shouldn't be necessary */
		.ui-button-icon-only .ui-button-text { padding: 0.35em; } 
		.ui-autocomplete-input { padding: 0.48em 0 0.47em 0.45em; }
	</style>
</head>
<body>
	
<div class="ui-widget">
	<label>Ihre bevorzugte Programmiersprache: </label>
	<select>
		<option value="a">asp</option>
        <option value="c">c</option>
        <option value="cpp">c++</option>
        <option value="cf">coldfusion</option>
        <option value="g">groovy</option>
        <option value="h">haskell</option>
        <option value="j">java</option>
        <option value="js">javascript</option>
        <option value="p1">perl</option>
        <option value="p2">php</option>
        <option value="p3">python</option>
        <option value="r">ruby</option>
        <option value="s">scala</option>
	</select>
</div>

Ein angepa&szlig;tes Widget aus der Kombination von Autocomplete und Button. Sie
k&ouml;nnen entweder etwas in das Feld schreiben, um eine gefilterte Liste basierend
auf Ihrer Eingabe zu erhalten, oder den Button verwenden, um die komplette Liste
zu sehen.<br /><br />

Die Liste wird aus einem existierenden Select-Feld gelesen und an Autocomplete
weitergeleitet.<br /><br />

</body>
</html>
