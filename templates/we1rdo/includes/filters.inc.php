			<div id="toolbar">
                <div class="notifications">
                    <?php if ($settings->get('show_multinzb')) { ?>
                    <p class="multinzb"><a class="button" onclick="downloadMultiNZB()" title="MultiNZB"><span class="count"></span></a><a class="clear" onclick="uncheckMultiNZB()" title="Reset selectie">[x]</a></p>
                    <?php } ?>
                </div>
                
				<div class="logininfo"><p><a onclick="toggleSidebarPanel('.userPanel')" class="user" title='Open "Gebruikers Paneel"'>
<?php if ($currentSession['user']['userid'] == 1) { ?>	
                	Inloggen
<?php } else { ?>
					<?php echo $currentSession['user']['firstname']; ?>
<?php } ?>
				</a></p></div>

                <span class="scroll"><input type="checkbox" name="filterscroll" id="filterscroll" value="Scroll" title="Wissel tussen vaste en meescrollende sidebar"><label>&nbsp;</label></span>

                <form id="filterform" action="">
<?php
	$activefilter = array_merge(array('type' => 'Titel', 'text' => '', 'tree' => '', 'unfiltered' => '', 'sortby' => $sortby, 'sortdir' => $sortdir), $activefilter);
	
	// Omdat we nu op meerdere criteria tegelijkertijd kunnen zoeken is dit onmogelijk
	// om 100% juist in de UI weer te geven. We doen hierdoor een gok die altijd juist
	// is zolang je maar zoekt via de UI.
	// Voor voor-gedefinieerde filters en dergelijke zal dit maar half juist zijn
	$searchType = 'Titel'; $searchText = '';
	if (isset($activefilter['filterValues'])) {
		foreach(array_keys($activefilter['filterValues']) as $filterType) {
			if (in_array($filterType, array('Titel', 'Poster', 'Tag', 'UserID'))) {
				$searchType = $filterType;
				$searchText = $activefilter['text'];
			}
		} # foreach
	} # if
	if (isset($activefilter['value'][0])) {
		$tmpSearch = explode(":", $activefilter['value'][0]);
		if (in_array($tmpSearch[0], array('Titel', 'Poster', 'Tag', 'UserID'))) {
			$searchText = $tmpSearch[1];
		} # if
	} # if
?>
                    <div><input type="hidden" id="search-tree" name="search[tree]" value="<?php echo $activefilter['tree']; ?>"></div>
<?php
	$filterColCount = 3;
	if ($settings->get('retrieve_full')) {
		$filterColCount++;
	} # if
?>
                    <div class="search"><input class='searchbox' type="text" name="search[text]" value="<?php echo htmlspecialchars($searchText); ?>"><input type='submit' class="filtersubmit" value='>>' title='Zoeken'></div>

                    <div class="sidebarPanel advancedSearch">
                    	<h4><a class="toggle" onclick="toggleSidebarPanel('.advancedSearch')" title='Sluit "Advanced Search"'>[x]</a>Zoeken op:</h4>
                        <ul class="search<?php if ($filterColCount == 3) {echo " small";} ?>">
                            <li> <input type="radio" name="search[type]" value="Titel" <?php echo $searchType == "Titel" ? 'checked="checked"' : "" ?> ><label>Titel</label></li>
                            <li> <input type="radio" name="search[type]" value="Poster" <?php echo $searchType == "Poster" ? 'checked="checked"' : "" ?> ><label>Poster</label></li>
                            <li> <input type="radio" name="search[type]" value="Tag" <?php echo $searchType == "Tag" ? 'checked="checked"' : "" ?> ><label>Tag</label></li>
<?php if ($settings->get('retrieve_full')) { ?>
                            <li> <input type="radio" name="search[type]" value="UserID" <?php echo $searchType == "UserID" ? 'checked="checked"' : "" ?> ><label>UserID</label></li>
<?php } ?>
                        </ul>

						<h4 class="sorting">Sorteren op:</h4>
                        <div><input type="hidden" name="sortdir" value="<?php if($activefilter['sortby'] == "stamp" || $activefilter['sortby'] == "spotrating" || $activefilter['sortby'] == "commentcount") {echo "DESC";} else {echo "ASC";} ?>"></div>
                        <ul class="search sorting">
                            <li> <input type="radio" name="sortby" value="" <?php echo ($activefilter['sortby'] == "" || $settings->get('search_def_sort') == 'relevance') ? 'checked="checked"' : "" ?>><label>Relevantie</label> </li>
                        	<li> <input type="radio" name="sortby" value="title" <?php echo ($activefilter['sortby'] == "title" || $settings->get('search_def_sort') == 'title') ? 'checked="checked"' : "" ?>><label>Titel</label> </li>
                            <li> <input type="radio" name="sortby" value="poster" <?php echo ($activefilter['sortby'] == "poster" || $settings->get('search_def_sort') == 'poster') ? 'checked="checked"' : "" ?>><label>Poster</label> </li>
                        	<li> <input type="radio" name="sortby" value="stamp" <?php echo ($activefilter['sortby'] == "stamp" || $settings->get('search_def_sort') == 'stamp') ? 'checked="checked"' : "" ?>><label>Datum</label> </li>
                            <li> <input type="radio" name="sortby" value="commentcount" <?php echo ($activefilter['sortby'] == "commentcount" || $settings->get('search_def_sort') == 'commentcount') ? 'checked="checked"' : "" ?>><label>Comments</label> </li>
                            <li> <input type="radio" name="sortby" value="spotrating" <?php echo ($activefilter['sortby'] == "spotrating" || $settings->get('search_def_sort') == 'spotrating') ? 'checked="checked"' : "" ?>><label>Rating</label> </li>
                        </ul>

						<h4>Filtering</h4>
                        <div class="unfiltered"><input type="checkbox" name="search[unfiltered]" value="true" <?php echo $activefilter['unfiltered'] == "true" ? 'checked="checked"' : "" ?>><label>Vergeet filters voor zoekopdracht</label></div>
    
                        <div id="tree"></div>
                    </div>
                </form>
                
                <div class="sidebarPanel userPanel">
                    <h4><a class="toggle" onclick="toggleSidebarPanel('.userPanel')" title='Sluit "Gebruikers paneel"'>[x]</a>Gebruikers paneel</h4>
                    <ul class="userInfo">
<?php if ($currentSession['user']['userid'] == 1) { ?>
						<li>U bent niet ingelogd</li>
<?php } else { ?>
						<li><?php echo "Gebruiker: <strong>" . $currentSession['user']['firstname'] . " " . $currentSession['user']['lastname'] . "</strong>"; ?></li>
						<li><?php echo "Laatst gezien: <strong>" . $tplHelper->formatDate($currentSession['user']['lastvisit'], 'lastvisit') . " geleden</strong>"; ?></li>
<?php } ?>
                    </ul>
                    
<?php if ($currentSession['user']['userid'] == 1) { ?>
                    <h4 class="dropDown"><span class="viewState"><a class="down" onclick="toggleCreateUser()"></a></span>Gebruiker toevoegen</h4>
                    <div class="createUser"></div>
<?php } ?>
                    
<?php if ($currentSession['user']['userid'] != 1) { ?>
					<h4 class="dropdown"><span class="viewState"><a class="down" onclick="toggleEditUser('<?php echo $currentSession['user']['userid'] ?>')"></a></span>Gebruiker wijzigen</h4>
					<div class="editUser"></div>
					
                    <h4>Uitloggen</h4>
                    <a onclick="userLogout()" class="greyButton">Uitloggen</a>
<?php } else { ?>
                    <h4>Inloggen</h4>
                    <div class="login"></div>
<?php } ?>
				</div>

                <div class="sidebarPanel sabnzbdPanel">
                	<h4><a class="toggle" onclick="toggleSidebarPanel('.sabnzbdPanel')" title='Sluit "SabNZBd paneel"'>[x]</a>SabNZBd</h4>
<?php 
	$nzbHandling = $this->_settings->get('nzbhandling'); 
	$sabnzbd = $nzbHandling['sabnzbd']; 
	$apikey = $tplHelper->apiToHash($sabnzbd['apikey']);
	echo "<input class='apikey' type='hidden' value='".$apikey."'>";
?>
                    <table class="sabInfo" summary="SABnzbd infomatie">
                    	<tr><td>Status:</td><td class="state"></td></tr>
                        <tr><td>Snelheid:</td><td class="speed"></td></tr>
                        <tr><td>Max. snelheid:</td><td class="speedlimit"></td></tr>
                        <tr><td>Te gaan:</td><td class="timeleft"></td></tr>
                        <tr><td>ETA:</td><td class="eta"></td></tr>
                        <tr><td>Wachtrij:</td><td class="mb"></td></tr>
                    </table>
                    <canvas id="graph" width="215" height="125"></canvas>
                    <table class="sabGraphData" summary="SABnzbd Graph Data" style="display:none;"><tbody><tr><td></td></tr></tbody></table>
					<h4>Wachtrij</h4>
					<table class="sabQueue" summary="SABnzbd queue"><tbody><tr><td></td></tr></tbody></table>
                </div>
            </div>

            <div id="filter" class="filter">					
                <h4><span class="viewState"><a onclick="toggleSidebarItem(this)"></a></span>Quick Links </h4>
                <ul class="filterlist quicklinks">
<?php foreach($quicklinks as $quicklink) {
			$newCount = ($settings->get('count_newspots') && stripos($quicklink[2], 'New:0')) ? $tplHelper->getNewCountForFilter($quicklink[2]) : "";
?>
					<li> <a class="filter <?php echo " " . $quicklink[3]; if (parse_url($tplHelper->makeSelfUrl("full"), PHP_URL_QUERY) == parse_url($tplHelper->makeBaseUrl("full") . $quicklink[2], PHP_URL_QUERY)) { echo " selected"; } ?>" href="<?php echo $quicklink[2]; ?>">
					<img src='<?php echo $quicklink[1]; ?>' alt='<?php echo $quicklink[0]; ?>'><?php echo $quicklink[0]; if ($newCount) { echo "<span class='newspots'>".$newCount."</span>"; } ?></a>
<?php } ?>
					</ul>
					
                    <h4><span class="viewState"><a onclick="toggleSidebarItem(this)"></a></span>Filters </h4>
                    <ul class="filterlist filters">

<?php
    foreach($filters as $filter) {
		$strFilter = $tplHelper->getPageUrl('index') . '&amp;search[tree]=' . $filter[2];
		$newCount = ($settings->get('count_newspots')) ? $tplHelper->getNewCountForFilter($strFilter) : "";
?>
						<li<?php if($filter[2]) { echo " class='". $tplHelper->filter2cat($filter[2]) ."'"; } ?>>
						<a class="filter<?php echo " " . $filter[3]; if ($tplHelper->makeSelfUrl("path") == $strFilter) { echo " selected"; } ?>" href="<?php echo $strFilter;?>">
						<img src='<?php echo $filter[1]; ?>' alt='<?php echo $filter[0]; ?>'><?php echo $filter[0]; if ($newCount) { echo "<span onclick=\"gotoNew('".$strFilter."')\" class='newspots' title='Laat nieuwe spots in filter &quot;".$filter[0]."&quot; zien'>$newCount</span>"; } ?><span class='toggle' title='Filter inklappen' onclick='toggleFilter(this)'>&nbsp;</span></a>
<?php
		if (!empty($filter[4])) {
			echo "\t\t\t\t\t\t\t<ul class='filterlist subfilterlist'>\r\n";
			foreach($filter[4] as $subFilter) {
				$strFilter = $tplHelper->getPageUrl('index') . '&amp;search[tree]=' . $subFilter[2];
				$newSubCount = ($settings->get('count_newspots')) ? $tplHelper->getNewCountForFilter($strFilter) : "";
?>
						<li> <a class="filter<?php echo " " . $subFilter[3]; if ($tplHelper->makeSelfUrl("path") == $strFilter) { echo " selected"; } ?>" href="<?php echo $strFilter;?>">
						<img src='<?php echo $subFilter[1]; ?>' alt='<?php echo $subFilter[0]; ?>'><?php echo $subFilter[0]; if ($newSubCount) { echo "<span onclick=\"gotoNew('".$strFilter."')\" class='newspots' title='Laat nieuwe spots in filter &quot;".$subFilter[0]."&quot; zien'>$newSubCount</span>"; } ?></a>
<?php
				if (!empty($subFilter[4])) {
					echo "\t\t\t\t\t\t\t<ul class='filterlist subfilterlist'>\r\n";
					foreach($subFilter[4] as $sub2Filter) {
						$strFilter = $tplHelper->getPageUrl('index') . '&amp;search[tree]=' . $sub2Filter[2];
						$newSub2Count = ($settings->get('count_newspots')) ? $tplHelper->getNewCountForFilter($strFilter) : "";
		?>
						<li> <a class="filter<?php echo " " . $sub2Filter[3]; if ($tplHelper->makeSelfUrl("path") == $strFilter) { echo " selected"; } ?>" href="<?php echo $strFilter;?>">
						<img src='<?php echo $sub2Filter[1]; ?>' alt='<?php echo $subFilter[0]; ?>'><?php echo $sub2Filter[0]; if ($newSub2Count) { echo "<span onclick=\"gotoNew('".$strFilter."')\" class='newspots' title='Laat nieuwe spots in filter &quot;".$sub2Filter[0]."&quot; zien'>$newSub2Count</span>"; } ?></a>
		<?php
					} # foreach 
					echo "\t\t\t\t\t\t\t</ul>\r\n";
				} # is_array
			
			} # foreach 
            echo "\t\t\t\t\t\t\t</ul>\r\n";
        } # is_array
    } # foreach
?>
                    </ul>

					<h4><span class="viewState"><a onclick="toggleSidebarItem(this)"></a></span>Onderhoud </h4>
					<ul class="filterlist maintenancebox">
						<li class="info"> Laatste update: <?php echo $tplHelper->formatDate($lastupdate, 'lastupdate'); ?> </li>
<?php if ($settings->get('show_updatebutton')) { ?>
						<li><a href="retrieve.php?output=xml" onclick="retrieveSpots()" class="greyButton retrievespots">Update Spots</a></li>
<?php } ?>
<?php if ($settings->get('keep_downloadlist')) { ?>
						<li><a href="<?php echo $tplHelper->getPageUrl('erasedls'); ?>" onclick="eraseDownloads()" class="greyButton erasedownloads">Verwijder downloadgeschiedenis</a></li>
<?php } ?>
						<li><a href="<?php echo $tplHelper->getPageUrl('markallasread'); ?>" onclick="markAsRead()" class="greyButton markasread">Markeer alles als gelezen</a></li>
					</ul>
				</div>