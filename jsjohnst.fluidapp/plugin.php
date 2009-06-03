<?php 

class FluidAppDataPlugin extends DevblocksPlugin {
   function load(DevblocksPluginManifest $manifest) {
   }

   static public function getTemplateHandler() {
      $tpl = DevblocksPlatform::getTemplateService();
      $tpl_path = dirname(__FILE__).'/templates/';
      $tpl->assign('path', $tpl_path);
      $tpl->cache_lifetime = "0";

      $total_new_count = 0;
      foreach(DAO_WorkflowView::getGroupTotals() as $data) {
         $total_new_count += $data["total"];
      }
      $tpl->assign('total_new_count', $total_new_count);

      $newest = isset($_SESSION["fluid_latest_seen"]) ? $_SESSION["fluid_latest_seen"] : time() - 3600;
      
      $worker = CerberusApplication::getActiveWorker();
      $memberships = $worker->getMemberships();

      $params = array(
         SearchFields_Ticket::TICKET_TEAM_ID => new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_TEAM_ID,'in',array_keys($memberships)),
         SearchFields_Ticket::TICKET_DELETED => new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_DELETED,'eq',0),
         SearchFields_Ticket::TICKET_WAITING => new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_WAITING,'eq',0),
         SearchFields_Ticket::TICKET_CLOSED => new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_CLOSED,'eq',0),
         SearchFields_Ticket::TICKET_UPDATED_DATE => new DevblocksSearchCriteria(SearchFields_Ticket::TICKET_UPDATED_DATE,'>', $newest),
      );

      list($results, $total) = DAO_Ticket::search(
                                array(SearchFields_Ticket::TICKET_ID, SearchFields_Ticket::TICKET_MASK, SearchFields_Ticket::TICKET_SUBJECT, SearchFields_Ticket::TICKET_LAST_WROTE), // cols
                                $params, // criteria
                                5, // rows
                                0, // page
                                SearchFields_Ticket::TICKET_UPDATED_DATE, // sort by
                                false, // sort asc
                                true // return count
                              );

      $tickets = array();
      foreach($results as $ticket) {
         if($ticket[SearchFields_Ticket::TICKET_UPDATED_DATE] > $newest) {
            $newest = $ticket[SearchFields_Ticket::TICKET_UPDATED_DATE];
         }
         $tickets[] = $ticket;
      }

      $_SESSION["fluid_latest_seen"] = $newest;

      $tpl->assign("recent_tickets_json", json_encode($tickets));
      $tpl->assign("recent_ticket_count", json_encode($total));

      return $tpl;
   }
};

class FluidAppDataPreBodyRenderer extends Extension_AppPreBodyRenderer {
   function render() {
      $tpl = FluidAppDataPlugin::getTemplateHandler();      
 
      $tpl->display('file:' . dirname(__FILE__) . '/templates/prebody.tpl');
   }
};


class FluidAppDataAPIFetch extends DevblocksControllerExtension {
   public function handleRequest(DevblocksHttpRequest $response) {
      $tpl = FluidAppDataPlugin::getTemplateHandler();

      header("Content-Type: text/javascript");
      $tpl->display('file:' . dirname(__FILE__) . '/templates/jsonp.tpl');
   }
};
