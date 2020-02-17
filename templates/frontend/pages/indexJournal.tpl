{**
 * templates/frontend/pages/indexJournal.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Display the index page for a journal
 *
 * @uses $currentJournal Journal This journal
 * @uses $journalDescription string Journal description from HTML text editor
 * @uses $homepageImage object Image to be displayed on the homepage
 * @uses $additionalHomeContent string Arbitrary input from HTML text editor
 * @uses $announcements array List of announcements
 * @uses $numAnnouncementsHomepage int Number of announcements to display on the
 *       homepage
 * @uses $issue Issue Current issue
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$currentJournal->getLocalizedName()}

<div class="page_index_journal">

	{call_hook name="Templates::Index::journal"}

	{*{if $homepageImage}
		<div class="homepage_image">
			<img src="{$publicFilesDir}/{$homepageImage.uploadName|escape:"url"}" alt="{$homepageImageAltText|escape}">
		</div>
	{/if}*}

	{if $journalDescription}
		<div class="journalDescriptionAbove journal-description">
			{$journalDescription}
		</div>
	{/if}

	{* Announcements *}
	{if $numAnnouncementsHomepage && $announcements|count}
		<section class="cmp_announcements media">
			<header class="page-header">
				<h2>
					{translate key="announcement.announcements"}
				</h2>
			</header>
			<div class="media-list">
				{foreach name=announcements from=$announcements item=announcement}
					{if $smarty.foreach.announcements.iteration > $numAnnouncementsHomepage}
						{break}
					{/if}
					{include file="frontend/objects/announcement_summary.tpl" heading="h3"}
				{/foreach}
			</div>
		</section>
	{/if}

	{* Announcements original 
	{if $numAnnouncementsHomepage && $announcements|@count}
		<div class="cmp_announcements highlight_first">
			<h2>
				{translate key="announcement.announcements"}
			</h2>
			{foreach name=announcements from=$announcements item=announcement}
				{if $smarty.foreach.announcements.iteration > $numAnnouncementsHomepage}
					{break}
				{/if}
				{if $smarty.foreach.announcements.iteration == 1}
					{include file="frontend/objects/announcement_summary.tpl" heading="h3"}
					<div class="more">
				{else}
					<article class="obj_announcement_summary">
						<h4>
							<a href="{url router=$smarty.const.ROUTE_PAGE page="announcement" op="view" path=$announcement->getId()}">
								{$announcement->getLocalizedTitle()|escape}
							</a>
						</h4>
						<div class="date">
							{$announcement->getDatePosted()}
						</div>
					</article>
				{/if}
			{/foreach}
			</div><!-- .more -->
		</div>
	{/if}~}

	{* Latest issue *}
	{if $issue}
		{if $homepageImage}
			<div class="current_issue dai_has_image">
				<h2>
					{translate key="journal.currentIssue"}
				</h2>
				<div class="current_issue_title">
					{$issue->getIssueIdentification()|strip_unsafe_html}
				</div>
				{include file="frontend/objects/issue_toc.tpl"}
				<a href="{url router=$smarty.const.ROUTE_PAGE page="issue" op="archive"}" class="read_more">
					{translate key="journal.viewAllIssues"}
				</a>
			</div>

		{else}
			<div class="current_issue" style="">
				<h2>
					{translate key="journal.currentIssue"}
				</h2>
				<div class="current_issue_title">
					{$issue->getIssueIdentification()|strip_unsafe_html}
				</div>
				{include file="frontend/objects/issue_toc.tpl"}
				<a href="{url router=$smarty.const.ROUTE_PAGE page="issue" op="archive"}" class="read_more">
					{translate key="journal.viewAllIssues"}
				</a>
			</div>
		{/if}
	{/if}

	{* Additional Homepage Content *}
	{if $additionalHomeContent}
		<div class="additional_content">
			{$additionalHomeContent}
		</div>
	{/if}

	{if $journalDescription}
		<div class="journalDescriptionBelow journal-description">
			{$journalDescription}
		</div>
	{/if}
</div><!-- .page -->

{include file="frontend/components/footer.tpl"}
