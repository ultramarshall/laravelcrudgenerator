<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" style="height:100%;">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li> <a class="waves-effect waves-dark" href="{{url(config('mixtra.ADMIN_PATH'))}}" aria-expanded="false"><i class="icon-speedometer"></i><span class="hide-menu">Dashboard</span></a>
                </li>
                @foreach(MITBooster::sidebarMenu() as $menu)
                <?php 
                    $href = $menu->is_broken?"javascript:alert('".trans('mixtra.controller_route_404')."')":$menu->url;
                    $class = 'has-arrow waves-effect waves-dark';
                    if(!$menu->children)
                        $class = '';
                ?>
                    <li data-id='{{$menu->id}}'> 
                        <a class="{{$class}}" 
                            href='{{ $href }}'
                            aria-expanded="false"><i class="{{$menu->icon}}"></i><span class="hide-menu">{{$menu->name}}</span></a>
                        <ul aria-expanded="false" class="collapse">
                        @if($menu->children)
                            @foreach($menu->children as $child)
                            <?php 
                                $href = $child->is_broken?"javascript:alert('".trans('mixtra.controller_route_404')."')":$child->url;
                            ?>
                                <li><a href='{{ $href }}'><i class='{{$child->icon}}'></i> <span>{{$child->name}}</span></a></li>
                            @endforeach
                        @endif
                        </ul>
                    </li>
                @endforeach


                @if(MITBooster::isSuperadmin())
                <li class="nav-small-cap">&nbsp;&nbsp;&nbsp;{{ trans('mixtra.SUPERADMIN') }}</li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-key"></i><span class="hide-menu">{{ trans('mixtra.Privileges_Roles') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('PrivilegesControllerGetAdd') }}">{{ trans('mixtra.Add_New_Privilege') }}</a></li>
                        <li><a href="{{ route('PrivilegesControllerGetIndex') }}">{{ trans('mixtra.List_Privilege') }}</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-users"></i><span class="hide-menu">{{ trans('mixtra.Users_Management') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('UsersControllerGetAdd') }}">{{ trans('mixtra.add_user') }}</a></li>
                        <li><a href="{{ route('UsersControllerGetIndex') }}">{{ trans('mixtra.List_users') }}</a></li>
                    </ul>
                </li>

                <li class="nav-small-cap">&nbsp;&nbsp;&nbsp;{{ trans('mixtra.CONFIGURATION') }}</li>
                <li><a href='{{Route("MenusControllerGetIndex")}}'><i class='fa fa-bars'></i><span class="hide-menu">{{ trans('mixtra.Menu_Management') }}</span></a></li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-th"></i><span class="hide-menu">{{ trans('mixtra.Modules') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href='{{ route("ModulesControllerGetAdd") }}'>{{ trans('mixtra.Add_New_Module') }}</a></li>
                        <li><a href='{{ route("ModulesControllerGetIndex") }}'>{{ trans('mixtra.List_Module') }}</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-wrench"></i><span class="hide-menu">{{ trans('mixtra.settings') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href='{{route("SettingsControllerGetAdd")}}'>{{ trans('mixtra.Add_New_Setting') }}</a></li>
                        <?php
                        $groupSetting = DB::table('mit_settings')->groupby('group_setting')->pluck('group_setting');
                        foreach($groupSetting as $gs):
                        ?>
                        <li class="<?=($gs == Request::get('group')) ? 'active' : ''?>">
                            <a href='{{route("SettingsControllerGetShow")}}?group={{ urlencode($gs) }}&m=0'>{{ $gs }}</a>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-envelope"></i><span class="hide-menu">{{ trans('mixtra.Email_Templates') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href='{{route("EmailTemplatesControllerGetAdd")}}'>{{ trans('mixtra.Add_New_Email') }}</a></li>
                        <li><a href='{{route("EmailTemplatesControllerGetIndex")}}'>{{ trans('mixtra.List_Email_Template') }}</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="fa fa-fire"></i><span class="hide-menu">{{ trans('mixtra.API_Generator') }}</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href='{{route("ApiCustomControllerGetGenerator")}}'>{{ trans('mixtra.Add_New_API') }}</a></li>
                        <li><a href='{{route("ApiCustomControllerGetIndex")}}'>{{ trans('mixtra.list_API') }}</a></li>
                        <li><a href='{{route("ApiCustomControllerGetScreetKey")}}'>{{ trans('mixtra.Secret_Key') }}</a></li>
                    </ul>
                </li>
                <li> <a class="waves-effect waves-dark" href="{{ route('LogsControllerGetIndex') }}" aria-expanded="false"><i class="fa fa-flag"></i><span class="hide-menu">{{ trans('mixtra.Log_User_Access') }}</span></a>
                </li>
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>