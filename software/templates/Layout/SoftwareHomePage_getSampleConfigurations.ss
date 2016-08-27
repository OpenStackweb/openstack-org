<% include SoftwareHomePage_MainNavMenu Active=2%>
<script type="application/javascript">

    var configuration_types              = new Object();
    var configuration_types_menu_list    = new Array();
    var default_configuration_type       = null;
    var max_maturity_points              = $Top.getMaxAllowedMaturityPoints;
    var release_core_component_count     = $Release.getOpenStackCoreComponentsCount;
    var release_optional_component_count = $Release.getOpenStackOptionalComponentsCount;

    <% loop Release.SampleConfigurationTypes.Sort(Order, ASC) %>
        <% if SampleConfigurations %>
            <% if $IsDefault %>
                default_configuration_type = $ID;
            <% end_if %>
        configuration_types[$ID] =
        {
            id: $ID,
            type: '{$Type}',
            is_default: $IsDefault,
            configurations : [
                    <% loop SampleConfigurations %>
                    {
                        id: $ID,
                        is_default: $IsDefault,
                        title: '{$Title}',
                        summary : '{$JS_val(Summary)}',
                        description : '{$JS_val(Description)}',
                        curator: {
                            id: $Curator.ID,
                            name: '{$Curator.FullName}',
                            position : '{$Curator.CurrentPosition}'
                        },
                        core_components : [
                            <% loop CoreComponents %>
                                {
                                    id: $ID,
                                    code_name : '{$CodeName}',
                                    icon_class: '{$IconClass}',
                                    release_slug : '{$Top.Release.Slug}',
                                    slug : '{$Slug}',
                                    name : '{$Name}',
                                    description : '{$JS_val(Description)}',
                                    adoption: $Top.Release.getComponentAdoption($ID),
                                    maturity_points: $Top.Release.getComponentMaturityPoints($ID),
                                    age: $Age,

                                },
                            <% end_loop %>

                        ],
                        missing_core_components : [
                        <% loop MissingCoreComponents %>
                            {
                                id: $ID,
                                code_name : '{$CodeName}',
                                icon_class: '{$IconClass}',
                                release_slug : '{$Top.Release.Slug}',
                                slug : '{$Slug}',
                                name : '{$Name}',
                                description : '{$JS_val(Description)}',
                                adoption: $Top.Release.getComponentAdoption($ID),
                                maturity_points: $Top.Release.getComponentMaturityPoints($ID),
                                age: $Age,
                            },
                        <% end_loop %>
                        ],
                        optional_components : [
                            <% loop OptionalComponents %>
                                {
                                    id: $ID,
                                    code_name : '{$CodeName}',
                                    name : '{$Name}',
                                    slug : '{$Slug}',
                                    release_slug : '{$Top.Release.Slug}',
                                    description : '{$JS_val(Description)}',
                                    adoption: $Top.Release.getComponentAdoption($ID),
                                    maturity_points: $Top.Release.getComponentMaturityPoints($ID),
                                    age: $Age
                                },
                            <% end_loop %>
                        ],
                        related_notes : [
                            <% loop RelatedNotes.Sort(Order, ASC) %>
                                {
                                    title : '{$Title}',
                                    link : '{$Link}'
                                },
                            <% end_loop %>
                        ]
                    },
                    <% end_loop %>
            ]
        };
        configuration_types_menu_list.push(configuration_types[$ID]);
        <% end_if %>
    <% end_loop %>

</script>
<div class="software-main-wrapper">
    <openstack-config-samples-types-nav default_configuration_type="{ default_configuration_type }" configuration_types_menu_list="{ configuration_types_menu_list }" configuration_types="{ configuration_types }"></openstack-config-samples-types-nav>

    <div class="container inner-software">

        <div class="sample-configs-tip">
            <div class="close-tip"><i class="fa fa-times"></i></div>
            <h5><i class="fa fa-question-circle"></i><%t Software.WHAT_ARE_SAMPLES 'What are sample configurations?' %></h5>
            <p>
                <%t Software.WHAT_ARE_SAMPLES_ANSWER 'Think of these as curated playlists of OpenStack configurations. These sample configurations are based on OpenStack case studies and real-world reference architectures across industries and workloads. Each configuration will give you a good idea of which core and optional projects can be used for different environments.' %>
            </p>
        </div>

        <openstack-config-samples base_url="{$Top.Link}" release_core_component_count="{ release_core_component_count }" release_optional_component_count="{ release_optional_component_count }" configuration_types="{ configuration_types }" max_maturity_points="{ max_maturity_points }"></openstack-config-samples>
        <!-- End Page Content -->
    </div>
</div>
<script src="software/js/public/software_sample_configs.bundle.js"></script>