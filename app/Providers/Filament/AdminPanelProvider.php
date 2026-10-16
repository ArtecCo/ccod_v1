<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ClientNotifications;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\Documentations\DocumentationResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AzureSubscription;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Support\FilamentStyling;
use Filament\Support\Facades\FilamentView;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Widgets\AccountWidget;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
 public function panel(Panel $panel):Panel
 {
  return $panel->default()->id('admin')->path('')->login()->topbar(false)->brandName(fn():string=>FilamentStyling::brandName())->brandLogo(fn():?string=>FilamentStyling::logoLight())->darkModeBrandLogo(fn():?string=>FilamentStyling::logoDark())->favicon(fn():?string=>FilamentStyling::favicon())->colors(FilamentStyling::colors())->sidebarWidth('16rem')->sidebarCollapsibleOnDesktop()->maxContentWidth(\Filament\Support\Enums\Width::Full)
   ->navigation(function(NavigationBuilder $builder):NavigationBuilder{
    $user=auth()->guard('web')->user();$authz=app(AccessAuthorizationService::class);$subscriptions=$user?$authz->accessibleSubscriptions($user):collect();
    $groups=$subscriptions->groupBy(function(AzureSubscription $subscription):string{$project=$subscription->projects->first();if($project===null||$project->businessUnit===null)return 'unassigned';return (string)$project->businessUnit->getKey().'|'.$project->getKey();})->sortBy(function($items):string{$subscription=$items->first();$project=$subscription->projects->first();return $project?->businessUnit?->name.'|'.$project?->name??'Unassigned subscriptions';})->map(function($items){$subscription=$items->first();$project=$subscription->projects->first();$businessUnit=$project?->businessUnit;$label=$project&&$businessUnit?$businessUnit->name.' / '.$project->name:'Unassigned subscriptions';return NavigationGroup::make($label)->icon(Heroicon::OutlinedCloud)->collapsible()->extraSidebarAttributes(['class'=>'ccod-sidebar-project-group'])->items($items->sortBy(fn(AzureSubscription $item):string=>$item->display_name)->map(fn(AzureSubscription $item)=>NavigationItem::make($item->display_name)->icon(Heroicon::OutlinedKey)->url(AzureSubscriptionResource::getUrl('view',['record'=>$item])))->all());})->values()->all();
    $management=[];if($user instanceof User){if($authz->hasPermission($user,'users.view'))$management[]=NavigationItem::make('Users')->icon(Heroicon::OutlinedUsers)->url(UserResource::getUrl());if($authz->hasPermission($user,'subscriptions.assign_business_unit'))$management[]=NavigationItem::make('Business Units')->icon(Heroicon::OutlinedUserGroup)->url(TeamResource::getUrl());if($authz->hasPermission($user,'projects.create')||$authz->hasPermission($user,'projects.edit')||$authz->hasPermission($user,'projects.delete'))$management[]=NavigationItem::make('Projects')->icon(Heroicon::OutlinedRectangleStack)->url(ProjectResource::getUrl());if($authz->hasPermission($user,'subscriptions.create')||$authz->hasPermission($user,'subscriptions.edit')||$authz->hasPermission($user,'subscriptions.delete')||$authz->hasPermission($user,'subscriptions.assign_environment')||$authz->hasPermission($user,'subscriptions.assign_business_unit'))$management[]=NavigationItem::make('Subscriptions')->icon(Heroicon::OutlinedCloud)->url(AzureSubscriptionResource::getUrl());if($authz->hasPermission($user,'roles.view'))$management[]=NavigationItem::make('Roles & Permissions')->icon(Heroicon::OutlinedShieldCheck)->url(RoleResource::getUrl());if($authz->hasPermission($user,'users.roles.assign'))$management[]=NavigationItem::make('Access Requests')->icon(Heroicon::OutlinedKey)->url(AccessRequestResource::getUrl());}
    $baseItems=[];if($user instanceof User&&$authz->hasPermission($user,'dashboard.view'))$baseItems[]=NavigationItem::make('Dashboard')->icon(Heroicon::OutlinedHome)->url(Dashboard::getUrl());$baseItems[]=NavigationItem::make('Notifications')->icon(Heroicon::OutlinedBell)->url(ClientNotifications::getUrl());if($user instanceof User&&$authz->hasPermission($user,'documentation.view'))$baseItems[]=NavigationItem::make('Documentation')->icon(Heroicon::OutlinedDocumentText)->url(DocumentationResource::getUrl());$base=NavigationGroup::make()->items($baseItems);$all=[$base,...$groups];if($management!==[])$all[]=NavigationGroup::make('Administration')->icon(Heroicon::OutlinedCog6Tooth)->items($management);return $builder->groups($all);
   })
   ->discoverResources(in:app_path('Filament/Resources'),for:'App\\Filament\\Resources')->discoverPages(in:app_path('Filament\\Pages'),for:'App\\Filament\\Pages')->pages([Dashboard::class,ClientNotifications::class,\App\Filament\Pages\Profile::class])->discoverWidgets(in:app_path('Filament/Widgets'),for:'App\\Filament\\Widgets')->widgets([AccountWidget::class])
   ->middleware([EncryptCookies::class,AddQueuedCookiesToResponse::class,StartSession::class,AuthenticateSession::class,ShareErrorsFromSession::class,VerifyCsrfToken::class,SubstituteBindings::class,DisableBladeIconComponents::class,DispatchServingFilamentEvent::class])->authMiddleware([Authenticate::class])->sidebarWidth('18rem')->sidebarCollapsibleOnDesktop()->sidebarCollapsibleOnDesktop()
   ->bootUsing(function(){FilamentView::registerRenderHook('panels::styles.after',fn():HtmlString=>new HtmlString('<style>.fi-sidebar-nav,.fi-sidebar-nav-groups,.fi-sidebar-group,aside.fi-sidebar nav,aside.fi-sidebar div{scrollbar-width:none!important;-ms-overflow-style:none!important}.fi-sidebar-nav::-webkit-scrollbar,.fi-sidebar-nav-groups::-webkit-scrollbar,.fi-sidebar-group::-webkit-scrollbar,aside.fi-sidebar nav::-webkit-scrollbar,aside.fi-sidebar div::-webkit-scrollbar{display:none!important;width:0;height:0;background:transparent}*{user-select:none!important}.fi-sidebar{box-shadow:inset -1px 0 0 rgba(0,0,0,.18)!important;transition:width .3s ease-in-out,transform .3s ease-in-out!important}.dark .fi-sidebar{box-shadow:inset 0 0 0 rgba(255,255,255,.18)!important}.fi-sidebar-nav{padding-top:.5rem!important}.fi-sidebar-item-grouped-border{display:none!important}.fi-sidebar-group .fi-sidebar-item-icon{display:block!important}.ccod-sidebar-project-group .fi-sidebar-group-label{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ccod-sidebar-project-name{display:inline-block;max-width:12rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:bottom}html{font-size:13px!important;scrollbar-gutter:stable}.fi-section,.fi-ta-ctn,.fi-wi-widget,.fi-card,.fi-modal-window{padding:.6rem!important;border-radius:var(--ccod-corner-radius,.375rem)!important}.fi-section-header,.fi-ta-header{padding-bottom:.35rem!important;margin-bottom:.35rem!important}.fi-sidebar-item-button{padding-top:.2rem!important;padding-bottom:.2rem!important;margin-top:.05rem!important;margin-bottom:.05rem!important}.fi-sidebar-group-label{padding-top:.2rem!important;padding-bottom:.2rem!important;margin-bottom:0!important}.fi-badge{font-size:.81rem!important;text-transform:capitalize!important}.fi-sidebar-nav-groups{gap:.25rem!important}.grid{gap:.6rem!important}.fi-fo-field-wrp{margin-bottom:.4rem!important}.fi-ta-table th{padding-top:1rem!important;padding-bottom:1rem!important}.fi-ta-table td{padding-top:.2rem!important;padding-bottom:.2rem!important}</style>'));FilamentView::registerRenderHook('panels::body.end',fn():string=>view('filament.partials.notification-poller')->render());FilamentView::registerRenderHook('panels::body.end',fn():string=>view('filament.partials.sidebar-collapse-all')->render());});
 }
}
