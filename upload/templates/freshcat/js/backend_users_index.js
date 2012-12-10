/**
 * This file is part of LEPTON Core, released under the GNU GPL
 * Please see LICENSE and COPYING files in your package for details, specially for terms and warranties.
 * 
 * NOTICE:LEPTON CMS Package has several different licenses.
 * Please see the individual license in the header of each single file or info.php of modules and templates.
 *
 * @author		  LEPTON Project
 * @copyright	   2012, LEPTON Project
 * @link			http://www.LEPTON-cms.org
 * @license		 http://www.gnu.org/licenses/gpl.html
 * @license_terms   please see LICENSE and COPYING files in your package
  *
 */

/**
 * check the checkboxes in an according div (given by class set_advanced___) and set an indivdual class if they are not equal. if equal according input gets same value
 *
 * @type plugin
 * @param  string  standard_class - standard class when all values are equal
 * @param  string  individual_class - individual class when values are different
 *
 **/
(function ($) {
	$.fn.set_individual_buttons = function (options)
	{
		var defaults =
		{
			standard_class:		'fc_checkbox_jq',
			individual_class:	'fc_checkbox_ind'
		};
		var options = $.extend(defaults, options);
		// Function to check whether all children inputs are checked=true, checked=false or different checked
		var check_inputs	= function(element)
		{
			var advanced			= match_class_prefix( 'set_advanced___', element ),
				advanced_div		= $('#' + advanced);

			if ( advanced_div.children('input').size() == advanced_div.children('input:checked').size() )
			{
				element.attr('checked' , true).addClass(options.standard_class).removeClass(options.individual_class);
			}
			else if ( advanced_div.children('input').size() == advanced_div.children('input').not(':checked').size() )
			{
				element.attr('checked' , false).addClass(options.standard_class).removeClass(options.individual_class);
			}
			else
			{
				element.attr('checked' , true).addClass(options.individual_class).removeClass(options.standard_class);
			}
		};
		return this.each(function ()
		{
			var element				= $(this),
				advanced			= match_class_prefix( 'set_advanced___', element ),
				advanced_div		= $('#' + advanced);

			// Function to check the each inputs in the advanced-div, to set the parent button to false/true/individual
			advanced_div.children('input').click( function()
			{
				check_inputs( element );
			});
			element.change( function()
			{
				var checked		= element.is(':checked');
				advanced_div.children('input').attr('checked' , checked);
				check_inputs( element );
			});
			// Initial calling of function
			check_inputs( element );
		});
		
	}
})(jQuery);

/**
 * activate click on a list element to get all contents of a group/user
 *
 * @type plugin
 * @param  string  get_url - url where to get the values
 * @param  string  activity_message - Message that will be shown in the activity div
 * @param  string  get_id - name of input element that contains the group_id/user_id
 *
 **/
(function ($) {
	$.fn.set_list_click = function (options)
	{
		var defaults	=
		{
			get_url:			ADMIN_URL + '/groups/ajax_get_group.php',
			activity_message:	'Loading group',
			addOnly:			$('.fc_addGroup'),
			modifyOnly:			$('.fc_modifyGroup'),
			get_id:				'group_id'
		};
		var options		= $.extend(defaults, options);
		return this.each(function ()
		{
			var element				= $(this);

			element.click( function(e)
			{
				e.preventDefault();
				var current		= $(this),
					dates		= {
						'id':			current.children('input[name=' + options.get_id + ']').val(),
						'leptoken':		getToken()
					};
				$.ajax(
				{
					type:		'POST',
					context:	current,
					url:		options.get_url,
					dataType:	'JSON',
					data:		dates,
					cache:		false,
					beforeSend:	function( data )
					{
						data.process	= set_activity( options.activity_message );
					},
					success:	function( data, textStatus, jqXHR  )
					{
						var current			= $(this),
							current_ul		= current.closest('ul');
				
						current_ul.children('li').not(current).removeClass('fc_active');
						$('#fc_list_add').removeClass('fc_active');
						current.addClass('fc_active');
			
						if ( data.success === true )
						{
							options.addOnly.hide();
							options.modifyOnly.show();

							$('#fc_Group_form, #fc_User_form').find('input[type=checkbox]').prop( 'checked', false );

							if ( options.get_id == 'group_id' )
							{
								$('#fc_Group_name').val(data.name);
								$('#fc_Group_group_id').val(data.group_id);

								$.each(data.system_permissions, function(index, value)
								{
									$('#fc_Group_' + value).prop( {checked: true} );
								});
								$.each(data.module_permissions, function(index, value)
								{
									$('#fc_Group_m_' + value).prop( {checked: true});
								});
								$.each(data.template_permissions, function(index, value)
								{
									$('#fc_Group_t_' + value).prop( {checked: true});
								});

								$('input[class*=set_advanced___]').unbind().set_individual_buttons();
							}
							else {
								$('#fc_User_name').val( data.username ).attr( 'name', data.username_fieldname );
								$('#fc_User_user_id').val( data.user_id );
								$('input[name=username_fieldname]').val( data.username_fieldname );
								$('#fc_User_display_name').val( data.display_name );
								$('#fc_User_email').val( data.email );
								$('#fc_User_password, #fc_User_password2').val('');
								$('#fc_User_active_user').prop( {checked: data.active});
								$('#fc_User_home_folder option').prop( {selected: false}).filter('[value="' + data.home_folder + '"]').prop( {selected: true});
								$.each(data.groups, function(index, value)
								{
									$('#fc_User_groups_' + value).prop( {checked: true});
								});							}
							return_success( jqXHR.process , data.message);
						}
						else {
							return_error( jqXHR.process , data.message);
						}
					},
					error:		function(jqXHR, textStatus, errorThrown)
					{
						alert(textStatus + ': ' + errorThrown );
					}
				});
			});
		})
	}
})(jQuery);

/**
 * check values for user if they fix to the challenges
 *
 * @type plugin
 * @param  string  get_url - url where to get the values
 * @param  string  activity_message - Message that will be shown in the activity div
 * @param  string  get_id - name of input element that contains the group_id/user_id
 *
 **/
function validateUserAdd(element)
{
	element.find('input:text').each(function()
	{
		var name = $(this).val();
		var rel = $(this).attr('rel');
		if( rel!='email' && name.length > rel )
		{
			$(this).removeClass('fc_invalid').addClass('fc_valid');
		}
		else if ( rel=='email' && isValidEmailAddress(name) )
		{
			$(this).removeClass('fc_invalid').addClass('fc_valid');
		}
		else
		{
			$(this).addClass('fc_invalid').removeClass('fc_valid');
		}
	});

	var pw1 = element.find('input:password').eq(0).val(),
		pw2 = element.find('input:password').eq(1).val();
	if( ( pw1 == pw2 ) && ( pw1.length > 5 ) )
	{
		// is valid
		element.find('input:password').removeClass('fc_invalid').addClass('fc_valid');
	}
	else
	{
		element.find('input:password').addClass('fc_invalid').removeClass('fc_valid');
	}

	if ( element.find('#fc_group input:checked').size() > 0 )
	{
		element.find('#fc_group').addClass('fc_valid').removeClass('fc_invalid');
	}
	else
	{
		element.find('#fc_group').removeClass('fc_valid').addClass('fc_invalid');
	}
	if ( element.find('.fc_invalid').size() > 0 )
	{
		$('.ui-dialog-buttonpane .submit').fadeOut(700);
	}
	else
	{
		$('.ui-dialog-buttonpane .submit').fadeIn(700);
	}
}

jQuery(document).ready(function()
{
	$('input[class*=set_advanced___]').set_individual_buttons();

	$('.fc_group_list').children('li').set_list_click();
	$('.fc_user_list').children('li').set_list_click(
	{
		get_url:			ADMIN_URL + '/users/ajax_get_user.php',
		activity_message:	'Loading user',
		addOnly:			$('.fc_addUser'),
		modifyOnly:			$('.fc_modifyUser'),
		get_id:				'user_id'
	});

	$('#fc_list_add').click( function(e)
	{
		e.preventDefault();

		var current		= $(this);

		$('#fc_list_overview').children('li').removeClass('fc_active');
		current.addClass('fc_active');

		$('.fc_modifyGroup, .fc_modifyUser').hide();
		$('.fc_addGroup, .fc_addUser').show();

		$('#fc_Group_form, #fc_User_form').find('input:checkbox').prop( 'checked', false );
		$('#fc_User_form select > option:first').prop( 'selected', true );
		$('#fc_Group_name, #fc_Group_group_id, #fc_User_form input:text, #fc_User_form input:password').val('').filter('#fc_Group_name').focus();
	}).click();

	$('#fc_Group_form input:reset, #fc_User_form input:reset').click( function(e)
	{
		e.preventDefault();
		$('#fc_lists_overview').find('.fc_active').click();
	});

	$('input[name=addGroup], input[name=saveGroup]').click( function(e)
	{
		e.preventDefault();
		var current					= $(this),
			currentForm				= current.closest('form'),
			dates					= {
				'leptoken':	getToken()
			};
		dates[current.attr('name')]	= current.val();
		currentForm.find('input[type=checkbox]:checked, input[type=text], #fc_Group_group_id').map( function()
		{
			return dates[$(this).attr('name')]	= $(this).val();
		});
		$.ajax(
		{
			type:		'POST',
			context:	current,
			url:		ADMIN_URL + '/groups/ajax_save_group.php',
			dataType:	'JSON',
			data:		dates,
			cache:		false,
			beforeSend:	function( data )
			{
				data.process	= set_activity( 'Saving group' );
			},
			success:	function( data, textStatus, jqXHR  )
			{
				if ( data.success === true )
				{
					return_success( jqXHR.process , data.message);
					
					if ( data.action == 'saved' )
					{
						$('#fc_list_overview').children('.fc_active').children('.fc_groups_name').text(data.name);
					}
					else {
						$('<li class="fc_group_item icon-users fc_border fc_gradient1 fc_gradient_hover"><span class="fc_groups_name">' + data.name + '</span><input type="hidden" name="group_id" value="' + data.id + '" /></li>').appendTo('#fc_list_overview').set_list_click().click();
					}
				}
				else {
					return_error( jqXHR.process , data.message);
				}
			},
			error:		function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus + ': ' + errorThrown );
			}
		});
	});
	$('input[name=addUser], input[name=saveUser]').click( function(e)
	{
		e.preventDefault();
		var current					= $(this),
			currentForm				= current.closest('form'),
			dates					= {
				'leptoken':		getToken(),
				'home_folder':	$('#fc_User_home_folder option:selected').val()
			},
			groups					= new Array();
		
		dates[current.attr('name')]	= current.val();
		currentForm.find('input[type=checkbox]:checked, input:text, input:password, #fc_User_user_id, #fc_User_fieldname').map( function()
		{
			var fieldname	= $(this).attr('name') == 'groups[]' ? 'groups' : $(this).attr('name');
			return dates[fieldname]	= $(this).attr('name') == 'groups[]' ? groups.push( $(this).val() ) : $(this).val();
		});
		dates['groups']		= groups;
		$.ajax(
		{
			type:		'POST',
			context:	current,
			url:		ADMIN_URL + '/users/ajax_save_user.php',
			dataType:	'JSON',
			data:		dates,
			cache:		false,
			beforeSend:	function( data )
			{
				data.process	= set_activity( 'Saving user' );
			},
			success:	function( data, textStatus, jqXHR  )
			{
				if ( data.success === true )
				{
					return_success( jqXHR.process , data.message);
					
					if ( data.action == 'saved' )
					{
						$('#fc_User_fieldname').val(data.username_fieldname);
						$('#fc_User_name').attr('name',data.username_fieldname);
						$('#fc_list_overview').children('.fc_active').children('.fc_display_name').text(data.display_name);
						$('#fc_list_overview').children('.fc_active').children('.fc_list_name').text(data.user_name);
					}
					else {
						$('<li class="fc_group_item icon-user fc_border fc_gradient1 fc_gradient_hover"><span class="fc_display_name">' + data.display_name + '</span><br/><span class="fc_list_name">' + data.username + '</span><input type="hidden" name="user_id" value="' + data.id + '" /></li>').appendTo('#fc_list_overview').set_list_click(
							{
								get_url:			ADMIN_URL + '/users/ajax_get_user.php',
								activity_message:	'Loading user',
								addOnly:			$('.fc_addUser'),
								modifyOnly:			$('.fc_modifyUser'),
								get_id:				'user_id'
							}).click();
					}
				}
				else {
					return_error( jqXHR.process , data.message);
				}
			},
			error:		function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus + ': ' + errorThrown );
			}
		});
	});

	$('#fc_removeGroup, #fc_removeUser').click( function(e)
	{
		e.preventDefault();
		var current		= $(this),
			kind		= current.attr('id') == 'fc_removeUser' ? 'user' : 'group',
			dates		= {
				'id':			kind == 'group' ? $('#fc_Group_group_id').val() : $('#fc_User_user_id').val(),
				'leptoken':		getToken()
			},
			current_li	= $('#fc_list_overview').children('.fc_active'),
			afterSend	= function( data, textStatus, jqXHR )
			{
				var current		= $(this);
				if ( $('#fc_list_overview').children('li').size() == 1 ) {
					$('#fc_list_add').click();
				}
				else if ( current.is(':last-child') )
				{
					current.prev('li').click();
				}
				else {
					current.next('li').click();
				}
				current.remove();
			},
			url		= kind == 'group' ? '/groups/ajax_delete_group.php' : '/users/ajax_delete_user.php';

		dialog_confirm( 'You really want to delete this ' + kind + '?', 'Removing group', ADMIN_URL + url, dates, 'POST', 'JSON', false, afterSend, current_li );
	});

	$('ul.fc_groups_tabs').find('a').click( function(e)
	{
		e.preventDefault();
		var current	= $(this),
			buttons	= current.closest('ul').find('a').not(current),
			rel		= current.attr('href'),
			tabs	= $('.fc_toggle_tabs');

		buttons.removeClass('fc_active');
		current.addClass('fc_active');

		tabs.not(rel).addClass('hidden');
		$(rel).removeClass('hidden');

	}).filter(':first').click();
});