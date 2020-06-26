<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="card card-default">
            <div class="card-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        OAuth Clients
                    </span>

                    <a class="action-link" tabindex="-1" @click="showCreateClientForm">
                        Create New Client
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Current Clients -->
                <p class="mb-0" v-if="clients.length === 0">
                    You have not created any OAuth clients.
                </p>

                <table class="table table-borderless mb-0" v-if="clients.length > 0">
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Name</th>
                            <th>Secret</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="client in clients">
                            <!-- ID -->
                            <td style="vertical-align: middle;">
                                {{ client.id }}
                            </td>

                            <!-- Name -->
                            <td style="vertical-align: middle;">
                                {{ client.name }}
                            </td>

                            <!-- Secret -->
                            <td style="vertical-align: middle;">
                                <code>{{ client.secret ? client.secret : '-' }}</code>
                            </td>

                            <!-- Edit Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link" tabindex="-1" @click="edit(client)">
                                    Edit
                                </a>
                            </td>

                            <!-- Delete Button -->
                            <td style="vertical-align: middle;">
                                <a class="action-link text-danger" @click="destroy(client)">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Client Modal -->
        <div class="modal fade" id="modal-create-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Create Client
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="createForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in createForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="create-client-name" type="text" class="form-control"
                                                                @keyup.enter="store" v-model="createForm.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                                    @keyup.enter="store" v-model="createForm.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>

                            <!-- Confidential -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Confidential</label>

                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" v-model="createForm.confidential">
                                        </label>
                                    </div>

                                    <span class="form-text text-muted">
                                        Require the client to authenticate with a secret. Confidential clients can hold credentials in a secure way without exposing them to unauthorized parties. Public applications, such as native desktop or JavaScript SPA applications, are unable to hold secrets securely.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="store">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" id="modal-edit-client" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Edit Client
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" class="form-control"
                                                                @keyup.enter="update" v-model="editForm.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                                    @keyup.enter="update" v-model="editForm.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="update">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Secret Modal -->
        <div class="modal fade" id="modal-client-secret" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Client Secret
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Here is your new client secret. This is the only time it will be shown so don't lose it!
                            You may now use this secret to make API requests.
                        </p>

                        <input type="text" class="form-control" v-model="clientSecret">
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                clients: [],

                clientSecret: null,

                createForm: {
                    errors: [],
                    name: '',
                    redirect: '',
                    confidential: true
                },

                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                }
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        methods: {
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.getClients();

                $('#modal-create-client').on('shown.bs.modal', () => {
                    $('#create-client-name').focus();
                });

                $('#modal-edit-client').on('shown.bs.modal', () => {
                    $('#edit-client-name').focus();
                });
            },

            /**
             * Get all of the OAuth clients for the user.
             */
            getClients() {
                var config = {
                    method: 'get',
                    url: 'api/oauth/clients',
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios(config)
                        .then(response => {
                            this.clients = response.data;
                        });
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
                $('#modal-create-client').modal('show');
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post',
                    '/oauth/clients',
                    this.createForm,
                    '#modal-create-client'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put',
                    '/oauth/clients/' + this.editForm.id,
                    this.editForm,
                    '#modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];
                var config = {
                    method: 'get',
                    url: 'api/oauth/personal-access-tokens',
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios[method](uri,config.headers, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');

                        if (response.data.plainSecret) {
                            this.showClientSecret(response.data.plainSecret);
                        }
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Show the given client secret to the user.
             */
            showClientSecret(clientSecret) {
                this.clientSecret = clientSecret;

                $('#modal-client-secret').modal('show');
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                var config = {
                    method: 'delete',
                    url: 'api/oauth/clients' + client.id,
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios(config)
                        .then(response => {
                            this.getClients();
                        });
            }
        }
    }
</script>
