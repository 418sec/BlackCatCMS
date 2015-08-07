/*
       2014, Black Cat Development
   @link            http://blackcat-cms.org
   @license         http://www.gnu.org/licenses/gpl.html
   @category        CAT_Core
   @package         freshcat

*/
jQuery(document).ready(function(){function f(a){$("#fc_list_overview").children("li.fc_not_installed").addClass("fc_no_search").hide();$(".icon-folder-add").hasClass("fc_active")&&($("#fc_list_overview").children("li.fc_not_installed.fc_type_heading").show(),$("button.fc_active").each(function(){var a=$(this);if(a.hasClass("icon-color-palette")){var b=$("#fc_list_overview").children("li.fc_type_templates.fc_not_installed");b.removeClass("fc_no_search").show()}a.hasClass("icon-puzzle")&&(b=$("#fc_list_overview").children("li.fc_type_modules.fc_not_installed"),
b.removeClass("fc_no_search").show());a.hasClass("icon-comments")&&(b=$("#fc_list_overview").children("li.fc_type_languages.fc_not_installed"),b.removeClass("fc_no_search").show())}));a.hasClass("icon-folder-add")&&(a.hasClass("fc_active")?$("#fc_list_overview").animate({scrollTop:$("li.fc_type_heading").offset().top},1E3):$("#fc_list_overview").animate({scrollTop:0},1E3))}$("#fc_list_overview li").fc_set_tab_list();$("#fc_mark_all").click(function(a){a.preventDefault();a=$(this);var c=$("#fc_perm_groups");
a.toggleClass("fc_marked");a.hasClass("fc_marked")?(c.children("input").prop("checked",!0).change(),a.children(".fc_mark").addClass("hidden"),a.children(".fc_unmark").removeClass("hidden")):(c.children("input").prop("checked",!1).change(),a.children(".fc_unmark").addClass("hidden"),a.children(".fc_mark").removeClass("hidden"))});$("#fc_list_overview").children("li").not(".fc_type_modules").addClass("fc_no_search").slideUp(0);$(".fc_not_installed").addClass("fc_no_search").slideUp(0);$("#fc_list_search_input").blur();
$("#fc_lists_overview button").not("#fc_list_add").click(function(){var a=$(this),c=$("#fc_list_overview").children("li.fc_type_modules").not(".fc_not_installed"),b=$("#fc_list_overview").children("li.fc_type_templates").not(".fc_not_installed"),d=$("#fc_list_overview").children("li.fc_type_languages").not(".fc_not_installed"),g=$("#fc_list_overview").children("li.fc_not_installed");a.toggleClass("fc_active");if(a.hasClass("icon-puzzle"))var e=c;else a.hasClass("icon-color-palette")?e=b:a.hasClass("icon-comments")?
e=d:a.hasClass("icon-folder-add")&&(e=g);a.hasClass("fc_active")?e.removeClass("fc_no_search").stop().slideDown(300):e.addClass("fc_no_search").stop().slideUp(300);f(a)});$(".fc_module_item").not(".fc_type_heading").click(function(a){a=$(this);a={_cat_ajax:1,module:a.find('input[name="addon_directory"]').val(),type:a.find('input[name="addon_type"]').val()};$.ajax({type:"POST",url:CAT_ADMIN_URL+"/addons/ajax_get_details.php",dataType:"json",data:a,cache:!1,beforeSend:function(a){a.process=set_activity()},
success:function(a,b,d){!0===a.success?($("div#addons_main_content").html(a.content),d.process.slideUp(1200,function(){d.process.remove()})):return_error(d.process,a.message)}})});$("#fc_addons_upload").unbind("click").bind("click",function(){$("ul.primary-nav > li").find("a").removeClass("current");$("ul.primary-nav > li#tab_item_1").find("a").addClass("current");$.ajax({type:"GET",data:{tpl:"backend_addons_index_upload"},url:CAT_ADMIN_URL+"/addons/ajax_get_template.php",dataType:"html",cache:!1,
success:function(a,c,b){$("div#addons_main_content").html(a)}})});$("#fc_addons_create").unbind("click").bind("click",function(){$("ul.primary-nav > li").find("a").removeClass("current");$("ul.primary-nav > li#tab_item_3").find("a").addClass("current");$.ajax({type:"GET",data:{tpl:"backend_addons_index_create"},url:CAT_ADMIN_URL+"/addons/ajax_get_template.php",dataType:"html",cache:!1,success:function(a,c,b){$("div#addons_main_content").html(a)}})});$("#fc_addons_catalog").unbind("click").bind("click",
function(){$("ul.primary-nav > li").find("a").removeClass("current");$("ul.primary-nav > li#tab_item_2").find("a").addClass("current");$.ajax({type:"POST",url:CAT_ADMIN_URL+"/addons/ajax_get_catalog.php",data:{_cat_ajax:1},dataType:"json",cache:!1,beforeSend:function(a){a.process=set_activity()},success:function(a,c,b){!0===a.success?($("div#addons_main_content").html(a.content),b.process.slideUp(1200,function(){b.process.remove()})):return_error(b.process,a.message)}})})});
