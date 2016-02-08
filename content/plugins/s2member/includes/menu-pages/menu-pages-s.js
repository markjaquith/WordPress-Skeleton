/**
* Core JavaScript routines for s2Member menu pages.
*
* Copyright: © 2009-2011
* {@link http://websharks-inc.com/ WebSharks, Inc.}
* (coded in the USA)
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package s2Member\Menu_Pages
* @since 3.0
*/
jQuery(document).ready(function($)
	{
		var esc_attr = esc_html = function(string/* Convert special characters. */)
		{
			if(/[&\<\>"']/.test(string = String(string)))
				string = string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'),
					string = string.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
			return string;
		};
		ws_plugin__s2member_generateSecurityKey = /* Generates a unique Security Key. */ function(obj)
			{
				if(!obj) obj = '#ws-plugin--s2member-sec-encryption-key';

				var mt_rand = /* The JS equivalent to mt_rand(). */ function(min, max)
					{
						min = (arguments.length < 1) ? 0 : min;
						max = (arguments.length < 2) ? 2147483647 : max;

						return Math.floor(Math.random() * (max-min+1))+min;
					};
				var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%^&*()';

				for(var i = 0, key = ''; i < 64; i++)
					key += chars.substr(mt_rand(0, chars.length-1), 1);

				$(obj).val(key);

				return false;
			};
		if(location.href.match /* Any & all s2Member pages. */(/page\=ws-plugin--s2member/))
			{
				$('input.ws-plugin--s2member-update-roles-button, input.ws-plugin--s2member-reset-roles-button').click(function()
					{
						var $this = /* Save $(this) into $this. */ $(this);
						$this.val /* Indicate loading status (please wait). */('one moment please ...');

						var levels = '<?php echo (int)$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; ?>';
						var resetUpdate = ($this.hasClass('ws-plugin--s2member-reset-roles-button')) ? 'Reset' : 'Update';

						$.post(ajaxurl, {action: 'ws_plugin__s2member_update_roles_via_ajax', ws_plugin__s2member_update_roles_via_ajax: '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--s2member-update-roles-via-ajax")); ?>'}, function(response)
							{
								if(response === '1')
									alert('s2Member\'s Roles/Capabilities '+((resetUpdate.toLowerCase() === 'reset') ? 'have been successfully reset' : 'updated successfully')+'.\nYour installation of s2Member has Membership Levels 0-'+levels+'.'), $this.val(resetUpdate+' Roles/Capabilities');

								else if(response === 'l' /* Locked? */)
									alert('Sorry, your request failed.\ns2Member\'s Roles/Capabilities are locked by Filter:\nws_plugin__s2member_lock_roles_caps'), $this.val(resetUpdate+' Roles/Capabilities');

								else // Default response message here.
									alert('Sorry, your request failed.\nAccess denied. Do you have the ability to `create_users`?'), $this.val(resetUpdate+' Roles/Capabilities');
							});
						return false;
					});
			}
		if(location.href.match(/page\=ws-plugin--s2member-logs/))
			{
				$('input#ws-plugin--s2member-gateway-debug-logs-extensive-1').click(function()
					{
						var $this = $(this), thisChecked = (this.checked) ? true : false;
						if(thisChecked) $('input#ws-plugin--s2member-gateway-debug-logs-1').attr('checked', 'checked');
					});
				var $toggles = $('a.ws-plugin--s2member-log-file-viewport-toggle'); $toggles.click(function()
					{
						$('textarea#ws-plugin--s2member-log-file-viewer').each(function()
                      {
                         var $viewer = $(this); // There is only one viewer.

	                      if($viewer.attr('data-state') !== 'expanded') // Not expanded yet?
		                      {
			                      $viewer.css({'height': ($viewer.prop('scrollHeight')+50)+'px', 'overflow-y':'auto'});
			                      $toggles.html('&#8657; normalize viewport &#10073;');
			                      $viewer.attr('data-state', 'expanded');
		                      }
                         else // Already expanded. We need to toggle it back to a normalized state in this case.
		                      {
			                      $viewer.css({'height': 'auto', 'overflow-y':'scroll'});
			                      $toggles.html('&#8659; expand viewport &#8659;');
			                      $viewer.attr('data-state', 'scrolling');
	                         }
                      });
						return false; // Prevents location.hash jumps.
					});
			}
		if(location.href.match(/page\=ws-plugin--s2member-mms-ops/))
			{
				$('select#ws-plugin--s2member-mms-registration-file').change(function()
					{
						if /* Expand/collapse relevant options; based on file selection. */($(this).val() === 'wp-signup')
							{
								var gv = $('select#ws-plugin--s2member-mms-registration-grants').val(), l0v = $('input#ws-plugin--s2member-mms-registration-blogs-level0').val();
								$('div#ws-plugin--s2member-mms-registration-support-package-details-wrapper').show(), $('div.ws-plugin--s2member-mms-registration-wp-login, table.ws-plugin--s2member-mms-registration-wp-login').hide(), $('div.ws-plugin--s2member-mms-registration-wp-signup, table.ws-plugin--s2member-mms-registration-wp-signup').show();
								$('div.ws-plugin--s2member-mms-registration-wp-signup-blogs-level0, table.ws-plugin--s2member-mms-registration-wp-signup-blogs-level0')[((gv === 'all') ? 'show' : 'hide')]();
								$('input#ws-plugin--s2member-mms-registration-blogs-level0').val(((gv === 'all') ? ((l0v > 0) ? l0v : '1') : '0'));
							}
						else if /* Expand/collapse relevant options. */($(this).val() === 'wp-login')
							{
								var gv = $('select#ws-plugin--s2member-mms-registration-grants').val(), l0v = $('input#ws-plugin--s2member-mms-registration-blogs-level0').val();
								$('div#ws-plugin--s2member-mms-registration-support-package-details-wrapper').hide(), $('div.ws-plugin--s2member-mms-registration-wp-login, table.ws-plugin--s2member-mms-registration-wp-login').show(), $('div.ws-plugin--s2member-mms-registration-wp-signup, table.ws-plugin--s2member-mms-registration-wp-signup').hide();
								$('div.ws-plugin--s2member-mms-registration-wp-signup-blogs-level0, table.ws-plugin--s2member-mms-registration-wp-signup-blogs-level0').hide();
								$('input#ws-plugin--s2member-mms-registration-blogs-level0').val('0');
							}
					}).trigger /* Fire on ready too. */('change');

				$('select#ws-plugin--s2member-mms-registration-grants').change(function()
					{
						$('select#ws-plugin--s2member-mms-registration-file').trigger('change');
					});
			}
		if(location.href.match(/page\=ws-plugin--s2member-gen-ops/))
			{
				ws_plugin__s2member_enableSecurityKey = /* Allow Security Key editing?? */ function()
					{
						if(confirm('Edit Key? Are you sure?\nThis could break your installation!\n\n*Note* If you\'ve been testing s2Member, feel free to change this Key before you go live. Just don\'t go live, and then change it. You\'ll have unhappy Customers. Data corruption WILL occur! For your safety, s2Member keeps a history of the last 10 Keys that you\'ve used. If you get yourself into a real situation, s2Member will let you revert back to a previous Key.'))
							$('input#ws-plugin--s2member-sec-encryption-key').removeAttr('disabled');

						return false;
					};
				ws_plugin__s2member_securityKeyHistory = /* Displays history of Keys. */ function()
					{
						$('div#ws-plugin--s2member-sec-encryption-key-history').toggle();

						return false;
					};
				$('select#ws-plugin--s2member-new-user-emails-enabled').change(function()
					{
						var $pluggable = $('input#ws-plugin--s2member-pluggables-wp-new-user-notification'), $this = $(this), $newUserEmails = $('div#ws-plugin--s2member-new-user-emails');

						if($pluggable.val() === '0' || $this.val() === '0')
							{
								($pluggable.val() === '0') ? $this.attr('disabled', 'disabled') : $this.removeAttr('disabled');
								$(':input', $newUserEmails).attr('disabled', 'disabled'), $newUserEmails.css('opacity', '0.5');
							}
						else // Else we allow the emails to be customized.
							$this.removeAttr('disabled'), $(':input', $newUserEmails).removeAttr('disabled'), $newUserEmails.css('opacity', '');

					}).trigger /* Fire on ready too. */('change');

				$('select#ws-plugin--s2member-login-reg-design-enabled').change(function()
               {
                  var $this = $(this), $loginRegDesign = $('div#ws-plugin--s2member-login-reg-design');

                  if($this.val() === '0') // Disable this section.
                     $(':input', $loginRegDesign).attr('disabled', 'disabled'), $loginRegDesign.css('opacity', '0.5'), $loginRegDesign.hide();

                  else // Else we allow login/registration design.
                     $(':input', $loginRegDesign).removeAttr('disabled'), $loginRegDesign.css('opacity', ''), $loginRegDesign.show();

               }).trigger /* Fire on ready too. */('change');

				$('select#ws-plugin--s2member-custom-reg-password').change(function()
					{
						var $this = $(this), $newUserNotification = $('div#ws-plugin--s2member-new-user-email-details');

						if($this.val() === '1') // Disable this section.
							$newUserNotification.css('opacity', '0.5');
						else $newUserNotification.css('opacity', '');

					}).trigger /* Fire on ready too. */('change');

				if($('input#ws-plugin--s2member-custom-reg-fields').length && $('div#ws-plugin--s2member-custom-reg-field-configuration').length)
					{
						( /* Wrap these routines inside a function to keep variables within relative scope. */function()
							{
								var i, fieldDefaults, tools, table, $tools, $table;
								var $fields = $('input#ws-plugin--s2member-custom-reg-fields');
								var $configuration = $('div#ws-plugin--s2member-custom-reg-field-configuration');
								var fields = ($fields.val()) ? $.JSON.parse($fields.val()) : [];

								fields = /* Force fields to an array. */ (fields instanceof Array) ? fields : [];

								fieldDefaults = {section: 'no', sectitle: '', id: '', label: '', type: 'text', deflt: '', options: '', expected: '', required: 'yes', levels: 'all', editable: 'yes', classes: '', styles: '', attrs: ''};

								for(i = 0; i < fields.length; i++)
									fields[i] = /* Extend, based on defaults (for future proofing). */ $.extend(true, {
									}, fieldDefaults, fields[i]);

								tools = '<div id="ws-plugin--s2member-custom-reg-field-configuration-tools"></div>', table = '<table id="ws-plugin--s2member-custom-reg-field-configuration-table"></table>';

								$configuration.html /* Add tools div & table div to configuration div (i.e., div#ws-plugin--s2member-custom-reg-field-configuration). */(tools+table);

								$tools = $('div#ws-plugin--s2member-custom-reg-field-configuration-tools'), $table = $('table#ws-plugin--s2member-custom-reg-field-configuration-table');

								ws_plugin__s2member_customRegFieldSectionChange = function(select)
									{
										var section = /* Current selection (no|yes, selected by site owner). */ $(select).val();

										var sectitle_trs = 'tr.ws-plugin--s2member-custom-reg-field-configuration-tools-form-sectitle';

										(section === 'yes') ? $(sectitle_trs).css('display', '') : $(sectitle_trs).css('display', 'none');
									};
								ws_plugin__s2member_customRegFieldTypeChange = /* Handle change events here. */ function(select)
									{
										var type = /* Current selection (type of Field, selected by site owner). */ $(select).val();

										var deflt_trs = 'tr.ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt', options_trs = 'tr.ws-plugin--s2member-custom-reg-field-configuration-tools-form-options', expected_trs = 'tr.ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected';

										(type.match(/^(text|textarea)$/)) ? $(deflt_trs).css('display', '') : $(deflt_trs).css('display', 'none');
										(type.match(/^(select|selects|checkboxes|radios)$/)) ? $(options_trs).css('display', '') : $(options_trs).css('display', 'none');
										(type.match(/^(text|textarea)$/)) ? $(expected_trs).css('display', '') : $(expected_trs).css('display', 'none');
									};
								ws_plugin__s2member_customRegFieldDelete = function(index)
									{
										var newFields = /* Build array. */ new Array();

										for(var i = 0; i < fields.length; i++)
											if /* Omit index. */(i !== index)
												newFields.push(fields[i]);

										fields = newFields, updateFields(), buildTable();
									};
								ws_plugin__s2member_customRegFieldMoveUp = function(index)
									{
										if(typeof fields[index] === 'object' && typeof fields[index-1] === 'object')
											{
												var prevFieldObj = fields[index-1], thisFieldObj = fields[index];

												fields[index-1] = thisFieldObj, fields[index] = prevFieldObj;

												updateFields(), buildTable();
											}
									};
								ws_plugin__s2member_customRegFieldMoveDown = function(index)
									{
										if(typeof fields[index] === 'object' && typeof fields[index+1] === 'object')
											{
												var nextFieldObj = fields[index+1], thisFieldObj = fields[index];

												fields[index+1] = thisFieldObj, fields[index] = nextFieldObj;

												updateFields(), buildTable();
											}
									};
								ws_plugin__s2member_customRegFieldCreate = function()
									{
										var $table = $('table#ws-plugin--s2member-custom-reg-field-configuration-tools-form'), field = {};

										$(':input[property]', $table).each( /* Go through each property value. */function()
											{
												var $this = $(this), property = $this.attr('property'), val = $.trim($this.val());

												field[property] = val;
											});
										if /* If it can be validated. */((field = validateField(field)))
											{
												fields.push(field), updateFields(), buildTools(), buildTable(), scrollReset();

												setTimeout( /* A momentary delay here for usability. */function()
													{
														var row = 'tr.ws-plugin--s2member-custom-reg-field-configuration-table-row-'+(fields.length-1);
														alert('Field created successfully.\n* Remember to "Save All Changes".');
														//$(row).effect('highlight');
													}, 500);
											}
									};
								ws_plugin__s2member_customRegFieldUpdate = function(index)
									{
										var $table = $('table#ws-plugin--s2member-custom-reg-field-configuration-tools-form'), field = {};

										$(':input[property]', $table).each( /* Go through each property value. */function()
											{
												var $this = $(this), property = $this.attr('property'), val = $.trim($this.val());

												field[property] = val;
											});
										if /* If it validates. */((field = validateField(field, index)))
											{
												fields[index] = field, updateFields(), buildTools(), buildTable(), scrollReset();

												setTimeout( /* A momentary delay here for usability. */function()
													{
														var row = 'tr.ws-plugin--s2member-custom-reg-field-configuration-table-row-'+index;
														alert('Field updated successfully.\n* Remember to "Save All Changes".');
														//$(row).effect('highlight');
													}, 500);
											}
									};
								ws_plugin__s2member_customRegFieldAdd = /* Add new field links. */ function()
									{
										buildTools /* No need to reset scroll position. */(true);
									};
								ws_plugin__s2member_customRegFieldEdit = /* Edit links. */ function(index)
									{
										buildTools(false, index), scrollReset();
									};
								ws_plugin__s2member_customRegFieldCancel = /* Cancel form. */ function()
									{
										buildTools(), scrollReset();
									};
								var validateField = function(field, index)
									{
										var editing = ( typeof index === 'number' && typeof fields[index] === 'object') ? true : false, errors = [], options, i;

										if(typeof field !== 'object' || typeof (field = $.extend(true, {}, fieldDefaults, field)) !== 'object')
											{
												alert('Invalid field object. Please try again.');
												return false;
											}
										field.sectitle = (field.section === 'yes') ? field.sectitle : '';
										field.deflt = (field.type.match(/^(text|textarea)$/)) ? field.deflt : '';
										field.deflt = (field.type.match(/^(text)$/)) ? field.deflt.replace(/[\r\n\t ]+/g, ' ') : field.deflt;
										field.options = (field.type.match(/^(select|selects|checkboxes|radios)$/)) ? field.options : '';
										field.expected = (field.type.match(/^(text|textarea)$/)) ? field.expected : '';

										if /* Every Field must have a unique ID. */(!field.id)
											{
												errors.push('Unique Field ID:\nThis is required. Please try again.');
											}
										else if(fieldIdExists(field.id) && (!editing || field.id !== fields[index].id))
											{
												errors.push('Unique Field ID:\nThat Field ID already exists. Please try again.');
											}

										if /* Every Field must have a label. */(!field.label)
											{
												errors.push('Field Label/Description:\nThis is required. Please try again.');
											}

										if(field.type.match(/^(select|selects|checkboxes|radios)$/) && !field.options)
											{
												errors.push('Option Configuration File:\nThis is required. Please try again.');
											}
										else if(field.type.match(/^(select|selects|checkboxes|radios)$/))
											{
												for(i = 0; i < (options = field.options.split(/[\r\n]+/)).length; i++)
													{
														if(!(options[i] = $.trim(options[i])).match(/^([^\|]*)(\|)([^\|]*)(\|default)?$/))
															{
																errors.push('Option Configuration File:\nInvalid configuration at line #'+(i+1)+'.');
																break; // Break now. There could potentially be lots of lines with errors.
															}
													}
												field.options = /* Clean up. */ $.trim(options.join('\n'));
											}

										if(!(field.levels = field.levels.replace(/ /g, '')))
											{
												errors.push('Applicable Levels:\nThis is required. Please try again.');
											}
										else if(!field.levels.match(/^(all|[0-9,]+)$/))
											{
												errors.push('Applicable Levels:\nShould be comma-delimited Levels, or just type: all.\n(examples: 0,1,2,3,4 or type the word: all)');
											}

										if(field.classes && field.classes.match(/[^a-z 0-9 _ \-]/i))
											{
												errors.push('CSS Classes:\nContains invalid characters. Please try again.\n(only: alphanumerics, underscores, hyphens, spaces)');
											}

										if(field.styles && field.styles.match(/["\=\>\<]/))
											{
												errors.push('CSS Styles:\nContains invalid characters. Please try again.\n(do NOT use these characters: = " < >)');
											}

										if(field.attrs && field.attrs.match(/[\>\<]/))
											{
												errors.push('Other Attributes:\nContains invalid characters. Please try again.\n(do NOT use these characters: < >)');
											}

										if /* Errors? */(errors.length > 0)
											{
												alert(errors.join('\n\n'));
												return false;
											}
										else // Return.
											return field;
									};
								var updateFields = /* Update hidden input value. */ function()
									{
										$fields.val(((fields.length > 0) ? $.JSON.stringify(fields) : ''));
									};
								var fieldId2Var = /* Convert ids to variables. */ function(fieldId)
									{
										return ( typeof fieldId === 'string') ? $.trim(fieldId).toLowerCase().replace(/[^a-z0-9]/g, '_') : '';
									};
								var fieldTypeDesc = function(type)
									{
										var types = {text: 'Text (single line)', textarea: 'Textarea (multi-line)', select: 'Select Menu (drop-down)', selects: 'Select Menu (multi-option)', checkbox: 'Checkbox (single)', pre_checkbox: 'Checkbox (pre-checked)', checkboxes: 'Checkboxes (multi-option)', radios: 'Radio Buttons (multi-option)'};

										if(typeof types[type] === 'string')
											return types[type];

										return /* Default. */ '';
									};
								var fieldIdExists = /* Already exists? */ function(fieldId)
									{
										for(var i = 0; i < fields.length; i++)
											if(fields[i].id === fieldId)
												return true;
									};
								var scrollReset = /* Return to Custom Fields section. */ function()
									{
										scrollTo(0, $('div.ws-plugin--s2member-custom-reg-fields-section').offset()['top']-100);
									};
								var buildTools = /* This builds tools into the configuration. */ function(adding, index)
									{
										var i = 0, html = '', form = '', w = 0, h = 0, editing = ( typeof index === 'number' && typeof fields[index] === 'object') ? true : false, displayForm = (adding || editing) ? true : false, field = (editing) ? $.extend(true, {
										}, fieldDefaults, fields[index]) : fieldDefaults;

										html += /* Click to add a new Custom Registration/Profile Field. */ '<a href="#" onclick="ws_plugin__s2member_customRegFieldAdd(); return false;">Add New Field</a>';

										tb_remove /* Remove an existing thickbox. */(), $('div#ws-plugin--s2member-custom-reg-field-configuration-thickbox-tools-form').remove();

										if /* Do we need to display the adding/editing form at all? */(displayForm)
											{
												form += '<div id="ws-plugin--s2member-custom-reg-field-configuration-thickbox-tools-form">';

												form += '<table id="ws-plugin--s2member-custom-reg-field-configuration-tools-form">';
												form += '<tbody>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-section">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-section">Starts A New Section?</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-section">';
												form += '<td colspan="2">';
												form += '<select property="section" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-section" onchange="ws_plugin__s2member_customRegFieldSectionChange(this);">';
												form += '<option value="no"'+((field.section === 'no') ? ' selected="selected"' : '')+'">No (this Field flows normally)</option>';
												form += '<option value="yes"'+((field.section === 'yes') ? ' selected="selected"' : '')+'">Yes (this Field begins a new section)</option>';
												form += '</select><br />';
												form += '<small>Optional. Allows Fields to be grouped into sections.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-sectitle ws-plugin--s2member-custom-reg-field-configuration-tools-form-section"'+((field.section === 'yes') ? '' : ' style="display:none;"')+'><td colspan="2"><hr /></td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-sectitle ws-plugin--s2member-custom-reg-field-configuration-tools-form-section"'+((field.section === 'yes') ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += 'Title for this new section? (optional)<br />';
												form += '<input type="text" property="sectitle" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-sectitle" value="'+esc_attr(field.sectitle)+'" /><br />';
												form += '<small>If empty, a simple divider will be used by default.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-type"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-type">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-type">Form Field Type: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-type">';
												form += '<td colspan="2">';
												form += '<select property="type" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-type" onchange="ws_plugin__s2member_customRegFieldTypeChange(this);">';
												form += '<option value="text"'+((field.type === 'text') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('text'))+'</option>';
												form += '<option value="textarea"'+((field.type === 'textarea') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('textarea'))+'</option>';
												form += '<option value="select"'+((field.type === 'select') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('select'))+'</option>';
												form += '<option value="selects"'+((field.type === 'selects') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('selects'))+'</option>';
												form += '<option value="checkbox"'+((field.type === 'checkbox') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('checkbox'))+'</option>';
												form += '<option value="pre_checkbox"'+((field.type === 'pre_checkbox') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('pre_checkbox'))+'</option>';
												form += '<option value="checkboxes"'+((field.type === 'checkboxes') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('checkboxes'))+'</option>';
												form += '<option value="radios"'+((field.type === 'radios') ? ' selected="selected"' : '')+'">'+esc_html(fieldTypeDesc('radios'))+'</option>';
												form += '</select><br />';
												form += '<small>The options below may change, based on the Field Type you choose here.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-label"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-label">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-label">Field Label/Desc: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-label">';
												form += '<td colspan="2">';
												form += '<input type="text" property="label" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-label" value="'+esc_attr(field.label)+'" /><br />';
												form += '<small>Examples: <code>Choose Country</code>, <code>Street Address</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-id"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-id">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-id">Unique Field ID: *</label></label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-id">';
												form += '<td colspan="2">';
												form += '<input type="text" property="id" maxlength="25" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-id" value="'+esc_attr(field.id)+'" /><br />';
												form += '<small>Examples: <code>country_code</code>, <code>street_address</code></small><br />';
												form += '<small>e.g., <code>[s2Get user_field="country_code" /]</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-required"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-required">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-required">Field Required: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-required">';
												form += '<td colspan="2">';
												form += '<select property="required" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-required">';
												form += '<option value="yes"'+((field.required === 'yes') ? ' selected="selected"' : '')+'">Yes (required)</option>';
												form += '<option value="no"'+((field.required === 'no') ? ' selected="selected"' : '')+'">No (optional)</option>';
												form += '</select><br />';
												form += '<small>If <code>yes</code>, only Users/Members will be "required" to enter this field.</small><br />';
												form += '<small>* Administrators are exempt from this requirement.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt">Default Text Value: (optional)</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<textarea property="deflt" wrap="off" spellcheck="false" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-deflt" rows="1">'+esc_html(field.deflt)+'</textarea><br />';
												form += '<small>Default value before user input is received.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-options"'+((field.type.match(/^(select|selects|checkboxes|radios)$/)) ? '' : ' style="display:none;"')+'><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-options"'+((field.type.match(/^(select|selects|checkboxes|radios)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-options">Option Configuration File: * (one option per line)</label><br />';
												form += '<small>Use a pipe <code>|</code> delimited format: <code>option value|option label</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-options"'+((field.type.match(/^(select|selects|checkboxes|radios)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<textarea property="options" wrap="off" spellcheck="false" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-options" rows="3">'+esc_html(field.options)+'</textarea><br />';
												form += 'Here is a quick example:<br />';
												form += '<small>You can also specify a <em>default</em> option:</small><br />';
												form += '<code>US|United States|default</code><br />';
												form += '<code>CA|Canada</code><br />';
												form += '<code>VI|Virgin Islands (U.S.)</code>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected">Expected Format: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected"'+((field.type.match(/^(text|textarea)$/)) ? '' : ' style="display:none;"')+'>';
												form += '<td colspan="2">';
												form += '<select property="expected" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-expected">';

												form += '<option value=""'+((field.expected === '') ? ' selected="selected"' : '')+'">Anything Goes</option>';
												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Specific Input Types">';
												form += '<option value="numeric-wp-commas"'+((field.expected === 'numeric-wp-commas') ? ' selected="selected"' : '')+'">Numeric (with or without decimals, commas allowed)</option>';
												form += '<option value="numeric"'+((field.expected === 'numeric') ? ' selected="selected"' : '')+'">Numeric (with or without decimals, no commas)</option>';
												form += '<option value="integer"'+((field.expected === 'integer') ? ' selected="selected"' : '')+'">Integer (whole number, without any decimals)</option>';
												form += '<option value="integer-gt-0"'+((field.expected === 'integer-gt-0') ? ' selected="selected"' : '')+'">Integer > 0 (whole number, no decimals, greater than 0)</option>';
												form += '<option value="float"'+((field.expected === 'float') ? ' selected="selected"' : '')+'">Float (floating point number, decimals required)</option>';
												form += '<option value="float-gt-0"'+((field.expected === 'float-gt-0') ? ' selected="selected"' : '')+'">Float > 0 (floating point number, decimals required, greater than 0)</option>';
												form += '<option value="date"'+((field.expected === 'date') ? ' selected="selected"' : '')+'">Date (required date format: dd/mm/yyyy)</option>';
												form += '<option value="email"'+((field.expected === 'email') ? ' selected="selected"' : '')+'">Email (require valid email)</option>';
												form += '<option value="url"'+((field.expected === 'url') ? ' selected="selected"' : '')+'">Full URL (starting with http or https)</option>';
												form += '<option value="domain"'+((field.expected === 'domain') ? ' selected="selected"' : '')+'">Domain Name (domain name only, without http)</option>';
												form += '<option value="phone"'+((field.expected === 'phone') ? ' selected="selected"' : '')+'">Phone # (10 digits w/possible hyphens,spaces,brackets)</option>';
												form += '<option value="uszip"'+((field.expected === 'uszip') ? ' selected="selected"' : '')+'">US Zipcode (5-9 digits w/possible hyphen)</option>';
												form += '<option value="cazip"'+((field.expected === 'cazip') ? ' selected="selected"' : '')+'">Canadian Zipcode (6 alpha-numerics w/possible space)</option>';
												form += '<option value="uczip"'+((field.expected === 'uczip') ? ' selected="selected"' : '')+'">US/Canadian Zipcode (either a US or Canadian zipcode)</option>';
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Any Character Combination">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="any-'+i+'"'+((field.expected === 'any-'+i) ? ' selected="selected"' : '')+'">Any Character Combination ('+i+' character minimum)</option>';
														form += '<option value="any-'+i+'-e"'+((field.expected === 'any-'+i+'-e') ? ' selected="selected"' : '')+'">Any Character Combination (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Alphanumerics, Spaces &amp; Punctuation Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="alphanumerics-spaces-punctuation-'+i+'"'+((field.expected === 'alphanumerics-spaces-punctuation-'+i) ? ' selected="selected"' : '')+'">Alphanumerics, Spaces &amp; Punctuation ('+i+' character minimum)</option>';
														form += '<option value="alphanumerics-spaces-punctuation-'+i+'-e"'+((field.expected === 'alphanumerics-spaces-punctuation-'+i+'-e') ? ' selected="selected"' : '')+'">Alphanumerics, Spaces &amp; Punctuation (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Alphanumerics &amp; Spaces Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="alphanumerics-spaces-'+i+'"'+((field.expected === 'alphanumerics-spaces-'+i) ? ' selected="selected"' : '')+'">Alphanumerics &amp; Spaces ('+i+' character minimum)</option>';
														form += '<option value="alphanumerics-spaces-'+i+'-e"'+((field.expected === 'alphanumerics-spaces-'+i+'-e') ? ' selected="selected"' : '')+'">Alphanumerics &amp; Spaces (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Alphanumerics &amp; Punctuation Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="alphanumerics-punctuation-'+i+'"'+((field.expected === 'alphanumerics-punctuation-'+i) ? ' selected="selected"' : '')+'">Alphanumerics &amp; Punctuation ('+i+' character minimum)</option>';
														form += '<option value="alphanumerics-punctuation-'+i+'-e"'+((field.expected === 'alphanumerics-punctuation-'+i+'-e') ? ' selected="selected"' : '')+'">Alphanumerics &amp; Punctuation (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Alphanumerics Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="alphanumerics-'+i+'"'+((field.expected === 'alphanumerics-'+i) ? ' selected="selected"' : '')+'">Alphanumerics ('+i+' character minimum)</option>';
														form += '<option value="alphanumerics-'+i+'-e"'+((field.expected === 'alphanumerics-'+i+'-e') ? ' selected="selected"' : '')+'">Alphanumerics (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Alphabetics Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="alphabetics-'+i+'"'+((field.expected === 'alphabetics-'+i) ? ' selected="selected"' : '')+'">Alphabetics ('+i+' character minimum)</option>';
														form += '<option value="alphabetics-'+i+'-e"'+((field.expected === 'alphabetics-'+i+'-e') ? ' selected="selected"' : '')+'">Alphabetics (exactly '+i+' character'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '<option disabled="disabled"></option>';

												form += '<optgroup label="Numeric Digits Only">';
												for(i = 1; i <= 25; i++)
													{
														form += '<option value="numerics-'+i+'"'+((field.expected === 'numerics-'+i) ? ' selected="selected"' : '')+'">Numeric Digits ('+i+' digit minimum)</option>';
														form += '<option value="numerics-'+i+'-e"'+((field.expected === 'numerics-'+i+'-e') ? ' selected="selected"' : '')+'">Numeric Digits (exactly '+i+' digit'+((i > 1) ? 's' : '')+')</option>';
													}
												form += '</optgroup>';

												form += '</select><br />';
												form += '<small>Only Users/Members will be required to meet this criteria.</small><br />';
												form += '<small>* Administrators are exempt from this.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-levels"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-levels">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-levels">Applicable Membership Levels: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-levels">';
												form += '<td colspan="2">';
												form += '<input type="text" property="levels" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-levels" value="'+esc_attr(field.levels)+'" /><br />';
												form += '<small>Please use comma-delimited Level #\'s: <code>0,1,2,3,4</code> or type: <code>all</code>.</small><br />';
												form += '<small>This allows you to enable this field - only at specific Membership Levels.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-editable"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-editable">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-editable">Allow Profile Edits: *</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-editable">';
												form += '<td colspan="2">';
												form += '<select property="editable" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-editable">';
												form += '<option value="yes"'+((field.editable === 'yes') ? ' selected="selected"' : '')+'">Yes (editable)</option>';
												form += '<option value="no"'+((field.editable === 'no') ? ' selected="selected"' : '')+'">No (uneditable after registration)</option>';
												form += '<option value="no-invisible"'+((field.editable === 'no-invisible') ? ' selected="selected"' : '')+'">No (uneditable &amp; totally invisible after registration)</option>';
												form += '<option value="no-always-invisible"'+((field.editable === 'no-always-invisible') ? ' selected="selected"' : '')+'">No (uneditable &amp; totally invisible, both during &amp; after registration)</option>';
												form += '<option value="yes-invisible"'+((field.editable === 'yes-invisible') ? ' selected="selected"' : '')+'">Yes (editable after registration / invisible during registration)</option>';
												form += '</select><br />';
												form += '<small>If <code>No</code>, this field will be un-editable after registration.</small><br />';
												form += '<small>* Administrators are exempt from this.</small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-classes"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-classes">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-classes">CSS Classes: (optional)</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-classes">';
												form += '<td colspan="2">';
												form += '<input type="text" property="classes" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-classes" value="'+esc_attr(field.classes)+'" /><br />';
												form += '<small>Example: <code>my-style-1 my-style-2</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-styles"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-styles">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-styles">CSS Styles: (optional)</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-styles">';
												form += '<td colspan="2">';
												form += '<input type="text" property="styles" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-styles" value="'+esc_attr(field.styles)+'" /><br />';
												form += '<small>Example: <code>color:#000000; background:#FFFFFF;</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-attrs"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-attrs">';
												form += '<td colspan="2">';
												form += '<label for="ws-plugin--s2member-custom-reg-field-configuration-tools-form-attrs">Other Attributes: (optional)</label>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-attrs">';
												form += '<td colspan="2">';
												form += '<input type="text" property="attrs" autocomplete="off" id="ws-plugin--s2member-custom-reg-field-configuration-tools-form-attrs" value="'+esc_attr(field.attrs)+'" /><br />';
												form += '<small>Example: <code>onkeyup="" onblur=""</code></small>';
												form += '</td>';
												form += '</tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-spacer ws-plugin--s2member-custom-reg-field-configuration-tools-form-buttons"><td colspan="2">&nbsp;</td></tr>';

												form += '<tr class="ws-plugin--s2member-custom-reg-field-configuration-tools-form-buttons">';
												form += '<td align="left">';
												form += '<input type="button" value="Cancel" onclick="ws_plugin__s2member_customRegFieldCancel();" />';
												form += '</td>';
												form += '<td align="right">';
												form += '<input type="button" value="'+((editing) ? 'Update This Field' : 'Create Registration Field')+'" onclick="'+((editing) ? 'ws_plugin__s2member_customRegFieldUpdate('+index+');' : 'ws_plugin__s2member_customRegFieldCreate();')+'" />';
												form += '</td>';
												form += '</tr>';

												form += '</tbody>';
												form += '</table>';

												form += '<div>';

												$('body').append(form);

												tb_show(((editing) ? 'Editing Registration/Profile Field' : 'New Custom Registration/Profile Field'), '#TB_inline?inlineId=ws-plugin--s2member-custom-reg-field-configuration-thickbox-tools-form');

												$('table#ws-plugin--s2member-custom-reg-field-configuration-tools-form').show();
											}
										$tools.html(html);
									};
								var buildTable = /* This builds the table of existing fields. */ function()
									{
										var l = fields.length, i = 0, html = '', eo = 'o';

										html += '<tbody>';

										html += '<tr>';
										html += '<th>Order</th>';
										html += '<th>Field Type</th>';
										html += '<th>Unique ID</th>';
										html += '<th>Required</th>';
										html += '<th>Levels</th>';
										html += '<th>- Tools -</th>';
										html += '</tr>';

										if /* Any fields? */(fields.length > 0)
											{
												for(i = 0; i < fields.length; i++)
													{
														html += '<tr class="'+esc_attr((eo = (eo === 'o') ? 'e' : 'o'))+((fields[i].section === 'yes') ? ' s' : '')+' ws-plugin--s2member-custom-reg-field-configuration-table-row-'+i+'">'; // Odd/even + row identifier.
														html += '<td nowrap="nowrap"><a class="ws-plugin--s2member-custom-reg-field-configuration-move-up" href="#" onclick="ws_plugin__s2member_customRegFieldMoveUp('+i+'); return false;"></a><a class="ws-plugin--s2member-custom-reg-field-configuration-move-down" href="#" onclick="ws_plugin__s2member_customRegFieldMoveDown('+i+'); return false;"></a></td>';
														html += '<td nowrap="nowrap">'+esc_html(fieldTypeDesc(fields[i].type))+'</td>';
														html += '<td nowrap="nowrap">'+esc_html(fields[i].id)+'</td>';
														html += '<td nowrap="nowrap">'+esc_html(fields[i].required)+'</td>';
														html += '<td nowrap="nowrap">'+esc_html(fields[i].levels)+'</td>';
														html += '<td nowrap="nowrap"><a class="ws-plugin--s2member-custom-reg-field-configuration-edit" href="#" onclick="ws_plugin__s2member_customRegFieldEdit('+i+'); return false;"></a><a class="ws-plugin--s2member-custom-reg-field-configuration-delete" href="#" onclick="ws_plugin__s2member_customRegFieldDelete('+i+'); return false;"></a></td>';
														html += '</tr>';
													}
											}
										else // Otherwise, there are no fields configured yet.
											{
												html += /* There are no fields yet. */ '<tr>';
												html += '<td colspan="6">No Custom Fields are configured.</td>';
												html += '</tr>';
											}

										html += '</tbody>';

										$table.html(html);
									};
								buildTools(), buildTable();
							})();
					}
			}
		if(location.href.match(/page\=ws-plugin--s2member-res-ops/))
			{
				$('input#ws-plugin--s2member-brute-force-restrictions-reset-button').click(function()
					{
						var $this = /* Save $(this) into $this. */ $(this);
						$this.val /* Indicate loading status (please wait). */('one moment please ...');

						$.post(ajaxurl, {action: 'ws_plugin__s2member_delete_reset_all_ip_restrictions_via_ajax', ws_plugin__s2member_delete_reset_all_ip_restrictions_via_ajax: '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--s2member-delete-reset-all-ip-restrictions-via-ajax")); ?>'}, function(response)
							{
								alert('s2Member\'s Brute Force Restriction Logs have all been reset.'), $this.val('Reset Brute Force Logs');
							});

						return false;
					});
				$('input#ws-plugin--s2member-ip-restrictions-reset-button').click(function()
					{
						var $this = /* Save $(this) into $this. */ $(this);
						$this.val /* Indicate loading status (please wait). */('one moment please ...');

						$.post(ajaxurl, {action: 'ws_plugin__s2member_delete_reset_all_ip_restrictions_via_ajax', ws_plugin__s2member_delete_reset_all_ip_restrictions_via_ajax: '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--s2member-delete-reset-all-ip-restrictions-via-ajax")); ?>'}, function(response)
							{
								alert('s2Member\'s IP Restriction Logs have all been reset.'), $this.val('Reset IP Restriction Logs');
							});
						return false;
					});
				$('div.ws-plugin--s2member-query-level-access-section input[type="checkbox"][name="ws_plugin__s2member_filter_wp_query\[\]"]').change(function()
					{
						var thisChange = /* Record value associated with change event. Allows for intutitive unchecking. */ $(this).val();

						$('div.ws-plugin--s2member-query-level-access-section input[type="checkbox"][name="ws_plugin__s2member_filter_wp_query\[\]"]').each(function()
							{
								var $this = $(this), val = $this.val(), checkboxes = 'input[type="checkbox"]';

								if /* All sub-items get checked/disabled. */(val === 'all' && this.checked)
									$this.nextAll(checkboxes).attr({'checked': 'checked', 'disabled': 'disabled'});

								else if(val === 'all' && !this.checked)
									{
										$this.nextAll(checkboxes).removeAttr('disabled');
										(thisChange === 'all') ? $this.nextAll(checkboxes).removeAttr('checked') : null;
									}
							});
					}).last().trigger('change');
			}
		if(location.href.match(/page\=ws-plugin--s2member-down-ops/))
			{
				var updateCloudFrontPrivateKey = /* Attaches to events below. */ function()
					{
						var $hiddenPrivateKey = $('input#ws-plugin--s2member-amazon-cf-files-private-key');
						var $visiblePrivateKeyEntry = $('textarea#ws-plugin--s2member-amazon-cf-files-private-key-entry');
						var hiddenPrivateKeyValue = $.trim($hiddenPrivateKey.val()), visiblePrivateKeyEntryValue = $.trim($visiblePrivateKeyEntry.val());

						if((hiddenPrivateKeyValue && !visiblePrivateKeyEntryValue) || visiblePrivateKeyEntryValue.match /* 9679.toString(16).toUpperCase() = 25CF. */(/[^\r\n\u25CF]/))
							$hiddenPrivateKey.val(visiblePrivateKeyEntryValue), $visiblePrivateKeyEntry.val(visiblePrivateKeyEntryValue.replace(/[^\r\n]/g, String.fromCharCode(9679)));
					};
				$('form#ws-plugin--s2member-options-form').submit(updateCloudFrontPrivateKey);
				$('textarea#ws-plugin--s2member-amazon-cf-files-private-key-entry').change(updateCloudFrontPrivateKey).trigger('change');

				var updateCloudFrontDistroCfgs = /* Attaches to events below. */ function()
					{
						var $hiddenPrivateKey = $('input#ws-plugin--s2member-amazon-cf-files-private-key');
						var $visiblePrivateKeyId = $('input#ws-plugin--s2member-amazon-cf-files-private-key-id');

						var $autoConfigDistros = $('input#ws-plugin--s2member-amazon-cf-files-auto-configure-distros');
						var $autoConfigDistrosStatus = $('input#ws-plugin--s2member-amazon-cf-files-distros-auto-config-status');

						var autoConfigDistrosStatusValue = $.trim($autoConfigDistrosStatus.val());
						var hiddenPrivateKeyValue = $.trim($hiddenPrivateKey.val()), visiblePrivateKeyIdValue = $.trim($visiblePrivateKeyId.val());
						var hiddenPrivateKeyPrevConfigValue = $.trim($hiddenPrivateKey.attr('data-s-prev-config-value')), visiblePrivateKeyIdPrevConfigValue = $.trim($visiblePrivateKeyId.attr('data-s-prev-config-value'));

						if(autoConfigDistrosStatusValue === 'configured' && ((visiblePrivateKeyIdPrevConfigValue && visiblePrivateKeyIdValue !== visiblePrivateKeyIdPrevConfigValue) || (hiddenPrivateKeyPrevConfigValue && hiddenPrivateKeyValue !== hiddenPrivateKeyPrevConfigValue)))
							{
								alert('s2Member will need to delete and re-configure your Amazon CloudFront distributions if you change this. When you\'re done editing, click (Save All Changes) below.');
								$autoConfigDistros.attr /* Forcibly check. */('checked', 'checked');
							}
						else if(autoConfigDistrosStatusValue !== 'configured' && visiblePrivateKeyIdValue && hiddenPrivateKeyValue)
							{
								alert('s2Member will need to auto-configure your Amazon CloudFront distributions for you. When you\'re done editing, click (Save All Changes) below.');
								$autoConfigDistros.attr /* Forcibly check. */('checked', 'checked');
							}
					};
				$('input#ws-plugin--s2member-amazon-cf-files-private-key-id').change(updateCloudFrontDistroCfgs);
				$('textarea#ws-plugin--s2member-amazon-cf-files-private-key-entry').change(updateCloudFrontDistroCfgs);

				$('input#ws-plugin--s2member-amazon-cf-files-auto-configure-distros-w-cnames').change(function()
					{
						var $this = $(this), thisChecked = (this.checked) ? true : false;
						var $autoConfigDistros = $('input#ws-plugin--s2member-amazon-cf-files-auto-configure-distros');
						var $autoConfigDistroCnames = $('div#ws-plugin--s2member-amazon-cf-files-auto-configure-distro-cnames');

						(thisChecked) ? $autoConfigDistroCnames.show() : $autoConfigDistroCnames.hide();
						(thisChecked) ? $autoConfigDistros.attr('checked', 'checked') : null;

					}).trigger('change');
			}
		if(location.href.match(/page\=ws-plugin--s2member-paypal-ops/))
			{
				$('select#ws-plugin--s2member-auto-eot-system-enabled').change(function()
					{
						var $this = $(this), val = $this.val();
						var $viaCron = $('p#ws-plugin--s2member-auto-eot-system-enabled-via-cron');

						if /* Display Cron instructions. */(val == 2)
							$viaCron.show()
						else // Hide instructions.
							$viaCron.hide();
					});
			}
		if(location.href.match(/page\=ws-plugin--s2member-paypal-buttons/))
			{
				$('div.ws-menu-page select[id]').filter( /* Filter all select elements with an id. */function()
					{
						return this.id.match(/^ws-plugin--s2member-(level[1-9][0-9]*|modification)-term$/);
					})
					.change(function()
					{
						var button = this.id.replace(/^ws-plugin--s2member-(.+?)-term$/g, '$1');
						var trialDisabled = ($(this).val().split('-')[2].replace(/[^0-1BN]/g, '') === 'BN') ? 1 : 0;

						$('p#ws-plugin--s2member-'+button+'-trial-line').css('display', (trialDisabled ? 'none' : ''));
						$('span#ws-plugin--s2member-'+button+'-trial-then').css('display', (trialDisabled ? 'none' : ''));
						$('span#ws-plugin--s2member-'+button+'-20p-rule').css('display', (trialDisabled ? 'none' : ''));

						(trialDisabled) ? $('input#ws-plugin--s2member-'+button+'-trial-period').val(0) : null;
						(trialDisabled) ? $('input#ws-plugin--s2member-'+button+'-trial-amount').val('0.00') : null;
					});
				$('div.ws-menu-page input[id]').filter( /* Filter all input elements with an id. */function()
					{
						return this.id.match(/^ws-plugin--s2member-(level[1-9][0-9]*|modification|ccap)-ccaps$/);
					})
					.keyup(function()
					{
						var value = this.value.replace(/^(-all|-al|-a|-)[;,]*/gi, ''), _all = (this.value.match(/^(-all|-al|-a|-)[;,]*/i)) ? '-all,' : '';
						if /* Only if there is a problem with the actual values; because this causes interruptions. */(value.match(/[^a-z_0-9,]/))
							this.value = _all+$.trim($.trim(value).replace(/[ \-]/g, '_').replace(/[^a-z_0-9,]/gi, '').toLowerCase());
					});
				ws_plugin__s2member_paypalButtonGenerate = /* Handles PayPal Button Generation. */ function(button)
					{
						var shortCodeTemplate = '[s2Member-PayPal-Button %%attrs%% image="default" output="button" /]', shortCodeTemplateAttrs = '', labels = {};

						eval("<?php echo c_ws_plugin__s2member_utils_strings::esc_dq($labels); ?>");

						var shortCode = $('input#ws-plugin--s2member-'+button+'-shortcode');
						var code = $('textarea#ws-plugin--s2member-'+button+'-button');
						var modLevel = $('select#ws-plugin--s2member-modification-level');

						var level = (button === 'modification') ? modLevel.val().split(':', 2)[1] : button.replace(/^level/, '');
						var label = /* Labels may NOT contain double-quotes. */ labels['level'+level].replace(/"/g, '');
						var desc = $.trim($('input#ws-plugin--s2member-'+button+'-desc').val().replace(/"/g, ""));

						var trialAmount = $('input#ws-plugin--s2member-'+button+'-trial-amount').val().replace(/[^0-9\.]/g, '');
						var trialPeriod = $('input#ws-plugin--s2member-'+button+'-trial-period').val().replace(/[^0-9]/g, '');
						var trialTerm = $('select#ws-plugin--s2member-'+button+'-trial-term').val().replace(/[^A-Z]/g, '');

						var regAmount = $('input#ws-plugin--s2member-'+button+'-amount').val().replace(/[^0-9\.]/g, '');
						var regPeriod = $('select#ws-plugin--s2member-'+button+'-term').val().split('-')[0].replace(/[^0-9]/g, '');
						var regTerm = $('select#ws-plugin--s2member-'+button+'-term').val().split('-')[1].replace(/[^A-Z]/g, '');
						var regRecur = $('select#ws-plugin--s2member-'+button+'-term').val().split('-')[2].replace(/[^0-1BN]/g, '');
						var /* These options are NOT yet configurable. */ regRecurTimes = '', regRecurRetry = '1';

						var /* NOT yet configurable. */ localeCode = '', digital = '0', noShipping = '1';
						var pageStyle = $.trim($('input#ws-plugin--s2member-'+button+'-page-style').val().replace(/"/g, ''));
						var currencyCode = $('select#ws-plugin--s2member-'+button+'-currency').val().replace(/[^A-Z]/g, '');

						var cCaps = $.trim($.trim($('input#ws-plugin--s2member-'+button+'-ccaps').val()).replace(/^(-all|-al|-a|-)[;,]*/gi, '').replace(/[ \-]/g, '_').replace(/[^a-z_0-9,]/gi, '').toLowerCase());
						cCaps = ($.trim($('input#ws-plugin--s2member-'+button+'-ccaps').val()).match(/^(-all|-al|-a|-)[;,]*/i)) ? ((cCaps) ? '-all,' : '-all')+cCaps.toLowerCase() : cCaps.toLowerCase();

						trialPeriod = /* Lifetime (1-L-BN) and Buy Now ( BN ) access is absolutely NOT compatible w/ Trial Periods. */ (regRecur === 'BN') ? '0' : trialPeriod;
						trialAmount = /* Validate Trial Amount. */ (!trialAmount || isNaN(trialAmount) || trialAmount < 0.01 || trialPeriod <= 0) ? '0' : trialAmount;

						var levelCcapsPer = (regRecur === 'BN' && regTerm !== 'L') ? level+':'+cCaps+':'+regPeriod+' '+regTerm : level+':'+cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace(/\:+$/g, '');

						if(trialAmount !== '0' && (isNaN(trialAmount) || trialAmount < 0.00))
							{
								alert('— Oops, a slight problem: —\n\nWhen provided, Trial Amount must be >= 0.00');
								return false;
							}
						else if(trialAmount !== '0' && /* Maximum. */ trialAmount > 10000.00 && currencyCode.toUpperCase() === 'USD')
							{
								alert('— Oops, a slight problem: —\n\nMaximum Trial Amount is: 10000.00');
								return false;
							}
						else if(trialTerm === 'D' && /* Some validation on the Trial Period. Max days: 90. */ trialPeriod > 90)
							{
								alert('— Oops, a slight problem: —\n\nMaximum Trial Days is: 90.\nIf you want to offer more than 90 days, please choose Weeks or Months from the drop-down.');
								return false;
							}
						else if(trialTerm === 'W' && /* Some validation on the Trial Period. 52 max. */ trialPeriod > 52)
							{
								alert('— Oops, a slight problem: —\n\nMaximum Trial Weeks is: 52.\nIf you want to offer more than 52 weeks, please choose Months from the drop-down.');
								return false;
							}
						else if(trialTerm === 'M' && /* Some validation on the Trial Period. 24 max. */ trialPeriod > 24)
							{
								alert('— Oops, a slight problem: —\n\nMaximum Trial Months is: 24.\nIf you want to offer more than 24 months, please choose Years from the drop-down.');
								return false;
							}
						else if(trialTerm === 'Y' && /* 5 years max. */ trialPeriod > 5)
							{
								alert('— Oops, a slight problem: —\n\nMax Trial Period Years is: 5.');
								return false;
							}
						else if(!regAmount || isNaN(regAmount) || regAmount < 0.01)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 0.01');
								return false;
							}
						else if(regAmount > /* $10,000.00 maximum. */ 10000.00 && currencyCode.toUpperCase() === 'USD')
							{
								alert('— Oops, a slight problem: —\n\nMaximum Amount is: 10000.00');
								return false;
							}
						else if /* Each Button should have a Description. */(!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}
						code.html(code.val().replace(/ \<\!--(\<input type\="hidden" name\="(amount|src|srt|sra|a1|p1|t1|a3|p3|t3)" value\="(.*?)" \/\>)--\>/g, " $1"));
						(parseInt(trialPeriod) <= 0) ? code.html(code.val().replace(/ (\<input type\="hidden" name\="(a1|p1|t1)" value\="(.*?)" \/\>)/g, " <!--$1-->")) : null;
						(regRecur === 'BN') ? code.html(code.val().replace(/ (\<input type\="hidden" name\="cmd" value\=")(.*?)(" \/\>)/g, " $1_xclick$3")) : null;
						(regRecur === 'BN') ? code.html(code.val().replace(/ (\<input type\="hidden" name\="(src|srt|sra|a1|p1|t1|a3|p3|t3)" value\="(.*?)" \/\>)/g, " <!--$1-->")) : null;
						(regRecur !== 'BN') ? code.html(code.val().replace(/ (\<input type\="hidden" name\="cmd" value\=")(.*?)(" \/\>)/g, " $1_xclick-subscriptions$3")) : null;
						(regRecur !== 'BN') ? code.html(code.val().replace(/ (\<input type\="hidden" name\="amount" value\="(.*?)" \/\>)/g, " <!--$1-->")) : null;

						shortCodeTemplateAttrs += /* For Modification Buttons. */ (button === 'modification') ? 'modify="1" ' : '';
						shortCodeTemplateAttrs += 'level="'+esc_attr(level)+'" ccaps="'+esc_attr(cCaps)+'" desc="'+esc_attr(desc)+'" ps="'+esc_attr(pageStyle)+'" lc="'+esc_attr(localeCode)+'" cc="'+esc_attr(currencyCode)+'" dg="'+esc_attr(digital)+'" ns="'+esc_attr(noShipping)+'" custom="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"';
						shortCodeTemplateAttrs += ' ta="'+esc_attr(trialAmount)+'" tp="'+esc_attr(trialPeriod)+'" tt="'+esc_attr(trialTerm)+'" ra="'+esc_attr(regAmount)+'" rp="'+esc_attr(regPeriod)+'" rt="'+esc_attr(regTerm)+'" rr="'+esc_attr(regRecur)+'" rrt="'+esc_attr(regRecurTimes)+'" rra="'+esc_attr(regRecurRetry)+'"';
						shortCode.val(shortCodeTemplate.replace(/%%attrs%%/, shortCodeTemplateAttrs));

						code.html(code.val().replace(/ name\="lc" value\="(.*?)"/, ' name="lc" value="'+esc_attr(localeCode)+'"'));
						code.html(code.val().replace(/ name\="no_shipping" value\="(.*?)"/, ' name="no_shipping" value="'+esc_attr(noShipping)+'"'));
						code.html(code.val().replace(/ name\="item_name" value\="(.*?)"/, ' name="item_name" value="'+esc_attr(desc)+'"'));
						code.html(code.val().replace(/ name\="item_number" value\="(.*?)"/, ' name="item_number" value="'+esc_attr(levelCcapsPer)+'"'));
						code.html(code.val().replace(/ name\="page_style" value\="(.*?)"/, ' name="page_style" value="'+esc_attr(pageStyle)+'"'));
						code.html(code.val().replace(/ name\="currency_code" value\="(.*?)"/, ' name="currency_code" value="'+esc_attr(currencyCode)+'"'));
						code.html(code.val().replace(/ name\="custom" value\="(.*?)"/, ' name="custom" value="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"'));

						code.html(code.val().replace(/ name\="modify" value\="(.*?)"/, ' name="modify" value="'+((button === 'modification') ? '1' : '0')+'"'));

						code.html(code.val().replace(/ name\="amount" value\="(.*?)"/, ' name="amount" value="'+esc_attr(regAmount)+'"'));

						code.html(code.val().replace(/ name\="src" value\="(.*?)"/, ' name="src" value="'+esc_attr(regRecur)+'"'));
						code.html(code.val().replace(/ name\="srt" value\="(.*?)"/, ' name="srt" value="'+esc_attr(regRecurTimes)+'"'));
						code.html(code.val().replace(/ name\="sra" value\="(.*?)"/, ' name="sra" value="'+esc_attr(regRecurRetry)+'"'));

						code.html(code.val().replace(/ name\="a1" value\="(.*?)"/, ' name="a1" value="'+esc_attr(trialAmount)+'"'));
						code.html(code.val().replace(/ name\="p1" value\="(.*?)"/, ' name="p1" value="'+esc_attr(trialPeriod)+'"'));
						code.html(code.val().replace(/ name\="t1" value\="(.*?)"/, ' name="t1" value="'+esc_attr(trialTerm)+'"'));
						code.html(code.val().replace(/ name\="a3" value\="(.*?)"/, ' name="a3" value="'+esc_attr(regAmount)+'"'));
						code.html(code.val().replace(/ name\="p3" value\="(.*?)"/, ' name="p3" value="'+esc_attr(regPeriod)+'"'));
						code.html(code.val().replace(/ name\="t3" value\="(.*?)"/, ' name="t3" value="'+esc_attr(regTerm)+'"'));

						$('div#ws-plugin--s2member-'+button+'-button-prev').html(code.val().replace(/\<form/, '<form target="_blank"').replace(/\<\?php echo S2MEMBER_VALUE_FOR_PP_INV\(\); \?\>/g, Math.round(new Date().getTime())+'~<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["REMOTE_ADDR"])); ?>').replace(/\<\?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_(ON0|OS0|ON1|OS1); \?\>/g, ''));

						(button === 'modification') ? alert('Your Modification Button has been generated.\nPlease copy/paste the Shortcode into your Login Welcome Page, or wherever you feel it would be most appropriate.\n\n* Remember, Modification Buttons should be displayed to existing Users/Members, and they should be logged-in, BEFORE clicking this Button.') : alert('Your Button has been generated.\nPlease copy/paste the Shortcode Format into your Membership Options Page.');

						shortCode.each( /* Focus and select the Shortcode. */function()
							{
								this.focus(), this.select();
							});
						return false;
					};
				ws_plugin__s2member_paypalCcapButtonGenerate = /* Handles PayPal Button Generation for Independent Capabilities. */ function()
					{
						var shortCodeTemplate = '[s2Member-PayPal-Button %%attrs%% image="default" output="button" /]', shortCodeTemplateAttrs = '';

						var shortCode = $('input#ws-plugin--s2member-ccap-shortcode');
						var code = $('textarea#ws-plugin--s2member-ccap-button');

						var desc = $.trim($('input#ws-plugin--s2member-ccap-desc').val().replace(/"/g, ''));

						var regAmount = $('input#ws-plugin--s2member-ccap-amount').val().replace(/[^0-9\.]/g, '');
						var regPeriod = $('select#ws-plugin--s2member-ccap-term').val().split('-')[0].replace(/[^0-9]/g, '');
						var regTerm = $('select#ws-plugin--s2member-ccap-term').val().split('-')[1].replace(/[^A-Z]/g, '');
						var regRecur = $('select#ws-plugin--s2member-ccap-term').val().split('-')[2].replace(/[^0-1BN]/g, '');

						var /* NOT yet configurable. */ localeCode = '', digital = '0', noShipping = '1';
						var pageStyle = $.trim($('input#ws-plugin--s2member-ccap-page-style').val().replace(/"/g, ''));
						var currencyCode = $('select#ws-plugin--s2member-ccap-currency').val().replace(/[^A-Z]/g, '');

						var cCaps = $.trim($.trim($('input#ws-plugin--s2member-ccap-ccaps').val()).replace(/^(-all|-al|-a|-)[;,]*/gi, '').replace(/[ \-]/g, '_').replace(/[^a-z_0-9,]/gi, '').toLowerCase());
						cCaps = ($.trim($('input#ws-plugin--s2member-ccap-ccaps').val()).match(/^(-all|-al|-a|-)[;,]*/i)) ? ((cCaps) ? '-all,' : '-all')+cCaps.toLowerCase() : cCaps.toLowerCase();

						var levelCcapsPer = (regRecur === 'BN' && regTerm !== 'L') ? '*:'+cCaps+':'+regPeriod+' '+regTerm : '*:'+cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace(/\:+$/g, '');

						if /* Must have some Independent Custom Capabilities. */(!cCaps || cCaps === '-all')
							{
								alert('— Oops, a slight problem: —\n\nPlease provide at least one Custom Capability.');
								return false;
							}
						else if(!regAmount || isNaN(regAmount) || regAmount < 0.01)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 0.01');
								return false;
							}
						else if /* $10,000.00 maximum. */(regAmount > 10000.00 && currencyCode.toUpperCase() === 'USD')
							{
								alert('— Oops, a slight problem: —\n\nMaximum Amount is: 10000.00');
								return false;
							}
						else if /* Each Button should have a Description. */(!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}

						shortCodeTemplateAttrs += 'level="*" ccaps="'+esc_attr(cCaps)+'" desc="'+esc_attr(desc)+'" ps="'+esc_attr(pageStyle)+'" lc="'+esc_attr(localeCode)+'" cc="'+esc_attr(currencyCode)+'" dg="'+esc_attr(digital)+'" ns="'+esc_attr(noShipping)+'"';
						shortCodeTemplateAttrs += ' custom="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>" ra="'+esc_attr(regAmount)+'" rp="'+esc_attr(regPeriod)+'" rt="'+esc_attr(regTerm)+'" rr="'+esc_attr(regRecur)+'"';
						shortCode.val(shortCodeTemplate.replace(/%%attrs%%/, shortCodeTemplateAttrs));

						code.html(code.val().replace(/ name\="lc" value\="(.*?)"/, ' name="lc" value="'+esc_attr(localeCode)+'"'));
						code.html(code.val().replace(/ name\="no_shipping" value\="(.*?)"/, ' name="no_shipping" value="'+esc_attr(noShipping)+'"'));
						code.html(code.val().replace(/ name\="item_name" value\="(.*?)"/, ' name="item_name" value="'+esc_attr(desc)+'"'));
						code.html(code.val().replace(/ name\="item_number" value\="(.*?)"/, ' name="item_number" value="'+esc_attr(levelCcapsPer)+'"'));
						code.html(code.val().replace(/ name\="page_style" value\="(.*?)"/, ' name="page_style" value="'+esc_attr(pageStyle)+'"'));
						code.html(code.val().replace(/ name\="currency_code" value\="(.*?)"/, ' name="currency_code" value="'+esc_attr(currencyCode)+'"'));
						code.html(code.val().replace(/ name\="custom" value\="(.*?)"/, ' name="custom" value="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"'));

						code.html(code.val().replace(/ name\="amount" value\="(.*?)"/, ' name="amount" value="'+esc_attr(regAmount)+'"'));

						$('div#ws-plugin--s2member-ccap-button-prev').html(code.val().replace(/\<form/, '<form target="_blank"').replace(/\<\?php echo S2MEMBER_VALUE_FOR_PP_INV\(\); \?\>/g, Math.round(new Date().getTime())+'~<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["REMOTE_ADDR"])); ?>').replace(/\<\?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_(ON0|OS0|ON1|OS1); \?\>/g, ''));

						alert('Your Button has been generated.\nPlease copy/paste the Shortcode into your Login Welcome Page, or wherever you feel it would be most appropriate.\n\n* Remember, Independent Custom Capability Buttons should ONLY be displayed to existing Users/Members, and they MUST be logged-in, BEFORE clicking this Button.');

						shortCode.each /* Focus and select the Shortcode. */(function()
							{
								this.focus(), this.select();
							});
						return false;
					};
				ws_plugin__s2member_paypalSpButtonGenerate = /* Handles PayPal Button Generation for Specific Post/Page Access. */ function()
					{
						var shortCodeTemplate = '[s2Member-PayPal-Button %%attrs%% image="default" output="button" /]', shortCodeTemplateAttrs = '';

						var shortCode = $('input#ws-plugin--s2member-sp-shortcode');
						var code = $('textarea#ws-plugin--s2member-sp-button');

						var leading = $('select#ws-plugin--s2member-sp-leading-id').val().replace(/[^0-9]/g, '');
						var additionals = $('select#ws-plugin--s2member-sp-additional-ids').val() || [];
						var hours = $('select#ws-plugin--s2member-sp-hours').val().replace(/[^0-9]/g, '');

						var regAmount = $('input#ws-plugin--s2member-sp-amount').val().replace(/[^0-9\.]/g, '');
						var desc = $.trim($('input#ws-plugin--s2member-sp-desc').val().replace(/"/g, ''));

						var /* NOT yet configurable. */ localeCode = '', digital = '0', noShipping = '1';
						var pageStyle = $.trim($('input#ws-plugin--s2member-sp-page-style').val().replace(/"/g, ''));
						var currencyCode = $('select#ws-plugin--s2member-sp-currency').val().replace(/[^A-Z]/g, '');

						if /* Must have a Leading Post/Page ID to work with. Otherwise, Link generation will fail. */(!leading)
							{
								alert('— Oops, a slight problem: —\n\nPlease select a Leading Post/Page.\n\n*Tip* If there are no Posts/Pages in the menu, it\'s because you\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.');
								return false;
							}
						else if(!regAmount || isNaN(regAmount) || regAmount < 0.01)
							{
								alert('— Oops, a slight problem: —\n\nAmount must be >= 0.01');
								return false;
							}
						else if /* $10,000.00 maximum. */(regAmount > 10000.00 && currencyCode.toUpperCase() === 'USD')
							{
								alert('— Oops, a slight problem: —\n\nMaximum Amount is: 10000.00');
								return false;
							}
						else if /* Each Button should have a Description. */(!desc)
							{
								alert('— Oops, a slight problem: —\n\nPlease type a Description for this Button.');
								return false;
							}
						for(var i = 0, ids = leading; i < additionals.length; i++)
							if(additionals[i] && additionals[i] !== leading)
								ids += ','+additionals[i];

						var /* Combined sp:ids:expiration hours. */ spIdsHours = 'sp:'+ids+':'+hours;

						shortCodeTemplateAttrs += 'sp="1" ids="'+esc_attr(ids)+'" exp="'+esc_attr(hours)+'" desc="'+esc_attr(desc)+'" ps="'+esc_attr(pageStyle)+'" lc="'+esc_attr(localeCode)+'" cc="'+esc_attr(currencyCode)+'" dg="'+esc_attr(digital)+'" ns="'+esc_attr(noShipping)+'"';
						shortCodeTemplateAttrs += ' custom="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>" ra="'+esc_attr(regAmount)+'"';
						shortCode.val(shortCodeTemplate.replace(/%%attrs%%/, shortCodeTemplateAttrs));

						code.html(code.val().replace(/ name\="lc" value\="(.*?)"/, ' name="lc" value="'+esc_attr(localeCode)+'"'));
						code.html(code.val().replace(/ name\="no_shipping" value\="(.*?)"/, ' name="no_shipping" value="'+esc_attr(noShipping)+'"'));
						code.html(code.val().replace(/ name\="item_name" value\="(.*?)"/, ' name="item_name" value="'+esc_attr(desc)+'"'));
						code.html(code.val().replace(/ name\="item_number" value\="(.*?)"/, ' name="item_number" value="'+esc_attr(spIdsHours)+'"'));
						code.html(code.val().replace(/ name\="page_style" value\="(.*?)"/, ' name="page_style" value="'+esc_attr(pageStyle)+'"'));
						code.html(code.val().replace(/ name\="currency_code" value\="(.*?)"/, ' name="currency_code" value="'+esc_attr(currencyCode)+'"'));
						code.html(code.val().replace(/ name\="custom" value\="(.*?)"/, ' name="custom" value="<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"])); ?>"'));

						code.html(code.val().replace(/ name\="amount" value\="(.*?)"/, ' name="amount" value="'+esc_attr(regAmount)+'"'));

						$('div#ws-plugin--s2member-sp-button-prev').html(code.val().replace(/\<form/, '<form target="_blank"').replace(/\<\?php echo S2MEMBER_VALUE_FOR_PP_INV\(\); \?\>/g, Math.round(new Date().getTime())+'~<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["REMOTE_ADDR"])); ?>').replace(/\<\?php echo S2MEMBER_CURRENT_USER_VALUE_FOR_PP_(ON0|OS0|ON1|OS1); \?\>/g, ''));

						alert('Your Button has been generated.\nPlease copy/paste the Shortcode into your WordPress Editor.');

						shortCode.each( /* Focus and select the Shortcode. */function()
							{
								this.focus(), this.select();
							});
						return false;
					};
				ws_plugin__s2member_paypalRegLinkGenerate = /* Handles PayPal Link Generation. */ function()
					{
						var level = $('select#ws-plugin--s2member-reg-link-level').val().replace(/[^0-9]/g, '');
						var subscrID = $.trim($('input#ws-plugin--s2member-reg-link-subscr-id').val());
						var custom = $.trim($('input#ws-plugin--s2member-reg-link-custom').val());
						var cCaps = $.trim($.trim($('input#ws-plugin--s2member-reg-link-ccaps').val()).replace(/[ \-]/g, '_').replace(/[^a-z_0-9,]/gi, '').toLowerCase());
						var fixedTerm = $.trim($('input#ws-plugin--s2member-reg-link-fixed-term').val().replace(/[^A-Z 0-9]/gi, '').toUpperCase());
						var $link = $('p#ws-plugin--s2member-reg-link'), $loading = $('img#ws-plugin--s2member-reg-link-loading');

						var levelCcapsPer = (fixedTerm && !fixedTerm.match(/L$/)) ? level+':'+cCaps+':'+fixedTerm : level+':'+cCaps;
						levelCcapsPer = /* Clean any trailing separators from this string. */ levelCcapsPer.replace(/\:+$/g, '');

						if /* We must have a Paid Subscr. ID value. */(!subscrID)
							{
								alert('— Oops, a slight problem: —\n\nPaid Subscr. ID is a required value.');
								return false;
							}
						else if(!custom || custom.indexOf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq ($_SERVER["HTTP_HOST"]); ?>') !== 0)
							{
								alert('— Oops, a slight problem: —\n\nThe Custom Value MUST start with your domain name.');
								return false;
							}
						else if /* Check format. */(fixedTerm && !fixedTerm.match(/^[1-9]+ (D|W|M|Y|L)$/))
							{
								alert('— Oops, a slight problem: —\n\nThe Fixed Term Length is not formatted properly.');
								return false;
							}
						$link.hide(), $loading.show(), $.post(ajaxurl, {action: 'ws_plugin__s2member_reg_access_link_via_ajax', ws_plugin__s2member_reg_access_link_via_ajax: '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--s2member-reg-access-link-via-ajax")); ?>', s2member_reg_access_link_subscr_gateway: 'paypal', s2member_reg_access_link_subscr_id: subscrID, s2member_reg_access_link_custom: custom, s2member_reg_access_link_item_number: levelCcapsPer}, function(response)
							{
								$link.show().html('<a href="'+esc_attr(response)+'" target="_blank" rel="external">'+esc_html(response)+'</a>'), $loading.hide();
							});
						return false;
					};
				ws_plugin__s2member_paypalSpLinkGenerate = /* Handles PayPal Link Generation. */ function()
					{
						var leading = $('select#ws-plugin--s2member-sp-link-leading-id').val().replace(/[^0-9]/g, '');
						var additionals = $('select#ws-plugin--s2member-sp-link-additional-ids').val() || [];
						var hours = $('select#ws-plugin--s2member-sp-link-hours').val().replace(/[^0-9]/g, '');
						var $link = $('p#ws-plugin--s2member-sp-link'), $loading = $('img#ws-plugin--s2member-sp-link-loading');

						if /* Must have a Leading Post/Page ID to work with. Otherwise, Link generation will fail. */(!leading)
							{
								alert('— Oops, a slight problem: —\n\nPlease select a Leading Post/Page.\n\n*Tip* If there are no Posts/Pages in the menu, it\'s because you\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.');
								return false;
							}
						for(var i = 0, ids = leading; i < additionals.length; i++)
							if(additionals[i] && additionals[i] !== leading)
								ids += ','+additionals[i];

						$link.hide(), $loading.show(), $.post(ajaxurl, {action: 'ws_plugin__s2member_sp_access_link_via_ajax', ws_plugin__s2member_sp_access_link_via_ajax: '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (wp_create_nonce ("ws-plugin--s2member-sp-access-link-via-ajax")); ?>', s2member_sp_access_link_ids: ids, s2member_sp_access_link_hours: hours}, function(response)
							{
								$link.show().html('<a href="'+esc_attr(response)+'" target="_blank" rel="external">'+esc_html(response)+'</a>'), $loading.hide();
							});
						return false;
					};
			}
		if(location.href.match(/page\=ws-plugin--s2member-els-ops/))
			{
				$('select#ws-plugin--s2member-custom-reg-opt-in').change(function()
					{
						var $this = $(this), val = $this.val();
						var $rows = $('tr.ws-plugin--s2member-custom-reg-opt-in-label-row');
						var $prevImg = $('img.ws-plugin--s2member-custom-reg-opt-in-label-prev-img');

						if /* Checkbox disabled. */(val <= 0)
							$rows.css('display', 'none'), $prevImg.attr('src', $prevImg.attr('src').replace(/\/checked\.png$/, '/unchecked.png'));

						else if /* Enabled, checked by default. */(val == 1)
							$rows.css('display', ''), $prevImg.attr('src', $prevImg.attr('src').replace(/\/unchecked\.png$/, '/checked.png'));

						else if /* Enabled, unchecked by default. */(val == 2)
							$rows.css('display', ''), $prevImg.attr('src', $prevImg.attr('src').replace(/\/checked\.png$/, '/unchecked.png'));
					});
				$('div.ws-plugin--s2member-opt-out-section input[type="checkbox"][name="ws_plugin__s2member_custom_reg_auto_opt_outs\[\]"]').change(function()
					{
						var /* Record value associated with change event. Also initialize checkedIndexes array. */ thisChange = $(this).val(), checkedIndexes = [];

						$('div.ws-plugin--s2member-opt-out-section input[type="checkbox"][name="ws_plugin__s2member_custom_reg_auto_opt_outs\[\]"]').each(function()
							{
								var $this = $(this), val = $this.val(), checkboxes = 'input[type="checkbox"]';

								if /* All sub-items get checked/disabled too. */(val === 'removal-deletion' && this.checked)
									$this.nextAll(checkboxes).slice(0, 2).attr({'checked': 'checked', 'disabled': 'disabled'});

								else if(val === 'removal-deletion' && !this.checked)
									{
										$this.nextAll(checkboxes).slice(0, 2).removeAttr('disabled');
										(thisChange === 'removal-deletion') ? $this.nextAll(checkboxes).slice(0, 2).removeAttr('checked') : null;
									}
								else if /* All sub-items get checked/disabled too. */(val === 'modification' && this.checked)
									$this.nextAll(checkboxes).slice(0, 3).attr({'checked': 'checked', 'disabled': 'disabled'});

								else if(val === 'modification' && !this.checked)
									{
										(thisChange === 'modification') ? $this.nextAll(checkboxes).slice(0, 3).removeAttr('checked') : null;
										$this.nextAll(checkboxes).slice(0, 3).removeAttr('disabled');
									}
							})
						.each( /* Now, which ones are checked? */function(index)
							{
								(this.checked) ? checkedIndexes.push(index) : null;
							});
						$('select#ws-plugin--s2member-custom-reg-auto-opt-out-transitions').removeAttr('disabled');
						if($.inArray(3, checkedIndexes) === -1 && $.inArray(4, checkedIndexes) === -1 && $.inArray(5, checkedIndexes) === -1 && $.inArray(6, checkedIndexes) === -1)
							$('select#ws-plugin--s2member-custom-reg-auto-opt-out-transitions').attr('disabled', 'disabled');

					}).last().trigger('change');
			}
	});
