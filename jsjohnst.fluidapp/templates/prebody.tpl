<script>
var fluid_cerberus_new_count = {$total_new_count};
var fluid_cerberus_tickets = {$recent_tickets_json};
var fluid_cerberus_webpath = "{devblocks_url}{/devblocks_url}";

{literal}
if(window.fluid) {
	window.console.log("start of cerberus script");
	window.fluid.addDockMenuItem("Home", function() { document.location.href = fluid_cerberus_webpath + "home"; });
	window.fluid.addDockMenuItem("View Tickets", function() { document.location.href = fluid_cerberus_webpath + "tickets"; });
	window.fluid.addDockMenuItem("Send Mail", function() { document.location.href = fluid_cerberus_webpath + "tickets/compose"; });
	window.fluid.addDockMenuItem("Address Book", function() { document.location.href = fluid_cerberus_webpath + "contacts"; });
	window.fluid.addDockMenuItem("My Account", function() { document.location.href = fluid_cerberus_webpath + "preferences"; });

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

	function loadFluidJSONPUrl(url) {
		var headID = document.getElementsByTagName("head")[0];         
		var newScript = document.createElement('script');
		    newScript.type = 'text/javascript';
		    newScript.src = url;
		    headID.appendChild(newScript);
	}
		
	window.setInterval(function() { loadFluidJSONPUrl(fluid_cerberus_webpath + "fluidapp.jsonp?v=" + (new Date()).getTime()); }, 15 * 1000);	
	window.console.log("end of cerberus script");
}
{/literal}
</script>
