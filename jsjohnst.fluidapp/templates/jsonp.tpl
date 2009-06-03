var fluid_cerberus_tickets = {$recent_tickets_json};
var fluid_cerberus_new_count = {$total_new_count};
var fluid_cerberus_webpath = "{devblocks_url}{/devblocks_url}";

{literal}
if (window.fluid) {
	window.fluid.dockBadge = fluid_cerberus_new_count ? fluid_cerberus_new_count : "";
	for(var i in fluid_cerberus_tickets) {
		var ticket = fluid_cerberus_tickets[i];
		window.fluid.showGrowlNotification({
			title: "Ticket Update",
			description: "From: " + ticket.t_last_wrote + "\n\n[" + ticket.t_mask + "] " + ticket.t_subject,
			priority: 1,
			sticky: true,
			identifier: ticket.t_id,
			onclick: function() {
				document.location.href = fluid_cerberus_webpath + "display/" + ticket.t_mask;
			} 
		});
	} 
}{/literal}
