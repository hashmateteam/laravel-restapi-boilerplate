<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div>
            <div class="card card-default">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>
                            Personal Access Tokens
                        </span>

                        <a class="action-link" tabindex="-1" @click="showCreateTokenForm">
                            Create New Token
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- No Tokens Notice -->
                    <p class="mb-0" v-if="tokens.length === 0">
                        You have not created any personal access tokens.
                    </p>

                    <!-- Personal Access Tokens -->
                    <table class="table table-borderless mb-0" v-if="tokens.length > 0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.name }}
                                </td>

                                <!-- Delete Button -->
                                <td style="vertical-align: middle;">
                                    <a class="action-link text-danger" @click="revoke(token)">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Token Modal -->
        <div class="modal fade" id="modal-create-token" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Create Token
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="form.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in form.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Create Token Form -->
                        <form role="form" @submit.prevent="store">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Name</label>

                                <div class="col-md-6">
                                    <input id="create-token-name" type="text" class="form-control" name="name" v-model="form.name">
                                </div>
                            </div>

                            <!-- Scopes -->
                            <div class="form-group row" v-if="scopes.length > 0">
                                <label class="col-md-4 col-form-label">Scopes</label>

                                <div class="col-md-6">
                                    <div v-for="scope in scopes">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    @click="toggleScope(scope.id)"
                                                    :checked="scopeIsAssigned(scope.id)">

                                                    {{ scope.id }}
                                            </label>
                                        </div>
                                    </div>
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

        <!-- Access Token Modal -->
        <div class="modal fade" id="modal-access-token" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Personal Access Token
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <p>
                            Here is your new personal access token. This is the only time it will be shown so don't lose it!
                            You may now use this token to make API requests.
                        </p>

                        <textarea class="form-control" rows="10">{{ accessToken }}</textarea>
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
                accessToken: null,

                tokens: [],
                scopes: [],

                form: {
                    name: '',
                    scopes: [],
                    errors: []
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
                this.getTokens();
                this.getScopes();

                $('#modal-create-token').on('shown.bs.modal', () => {
                    $('#create-token-name').focus();
                });
            },

            /**
             * Get all of the personal access tokens for the user.
             */
            getTokens() {
                var config = {
                    method: 'get',
                    url: 'oauth/personal-access-tokens',
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios(config)
                        .then(response => {
                            this.tokens = response.data;
                        });
            },

            /**
             * Get all of the available scopes.
             */
            getScopes() {
                var config = {
                    method: 'get',
                    url: 'oauth/scopes',
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios(config)
                        .then(response => {
                            this.scopes = response.data;
                        });
            },

            /**
             * Show the form for creating new tokens.
             */
            showCreateTokenForm() {
                $('#modal-create-token').modal('show');
            },

            /**
             * Create a new personal access token.
             */
            store() {
                this.accessToken = null;

                this.form.errors = [];
                var config = {
                    method: 'get',
                    url: 'oauth/personal-access-tokens',
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios.post('api/oauth/personal-access-tokens',config.headers, this.form)
                        .then(response => {
                            this.form.name = '';
                            this.form.scopes = [];
                            this.form.errors = [];

                            this.tokens.push(response.data.token);

                            this.showAccessToken(response.data.accessToken);
                        })
                        .catch(error => {
                            if (typeof error.response.data === 'object') {
                                this.form.errors = _.flatten(_.toArray(error.response.data.errors));
                            } else {
                                this.form.errors = ['Something went wrong. Please try again.'];
                            }
                        });
            },

            /**
             * Toggle the given scope in the list of assigned scopes.
             */
            toggleScope(scope) {
                if (this.scopeIsAssigned(scope)) {
                    this.form.scopes = _.reject(this.form.scopes, s => s == scope);
                } else {
                    this.form.scopes.push(scope);
                }
            },

            /**
             * Determine if the given scope has been assigned to the token.
             */
            scopeIsAssigned(scope) {
                return _.indexOf(this.form.scopes, scope) >= 0;
            },

            /**
             * Show the given access token to the user.
             */
            showAccessToken(accessToken) {
                $('#modal-create-token').modal('hide');

                this.accessToken = accessToken;

                $('#modal-access-token').modal('show');
            },

            /**
             * Revoke the given token.
             */
            revoke(token) {
                var config = {
                    method: 'delete',
                    url: 'oauth/personal-access-tokens/' + token.id,
                    headers: { 
                        'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNzQ0MmE2NDE2ZGNiMTRhYWQ4ZjU4YzI4MTNhMTM0MWY5Njc0NDY1OWRlZjRhNWI2ZGE2N2E5ZGMwNjAxMGZhYjk2MzZkMzk4N2I4YmZjMGYiLCJpYXQiOjE1OTMxNzEyNjQsIm5iZiI6MTU5MzE3MTI2NCwiZXhwIjoxNjI0NzA3MjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.r-TRwwoiKiYlapILXKb5yRIDO71LT14B0sC0GvDLzhXh-EXVkCKnVSUbahfRxHwtJWDgwGFZ4grrlRZxXNiwO8FcWFy6z2ATXBTbEfbzevh2r04PfjwF7Pt_bFT9y8RVwAPHTKD_Jr2omD_cpTz6lMkMXkUo70jm6yOFtSWRpM-zcvezGvjsxmulAwuQzqfENjc-IjgvmC9emT4oJKhYPNc-wED6uSYK6btuf4JBzA7tiU6veVBlpWvEMmChoCBPO7U0gfnSZPo9oKojYFuxypnGO2gTLOq6QGvU4Ldy5ZiXsXJXSJt7OO_8sPh4T2HJnWJpyrthyRrDH9GNKlMbZQVPbrlXFOym9t8te0U3zOzNbLnB0Zc1IMAnlW6YqPEidc6jYhCgPSb59hC9ZJ4iFGGmEvXsH6IFRIjeB_Dui6aJdsfJ5iB6oEBYxYePobo2kLKzbtAc9PscqpVy34JeNACPZQWGAOPYI_VZF6H6rxjCuRzHaBC0Ltj-JY-Yb-tDU6w4EAZpG3eGiou9ApSa6641FyhlBEz4bj2lhWt1WqPA_CJfP1mUT7PXpmPL-2sjvASbwX9SooC56meFm3nux114IGUCe6PFGT8sSb-vlcRAmxd_drYl3--q0xg_hivSUiPpGOZ6i0XOqEVauNtjjfdgSbgjIEM04vFzdiv07z8',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                };
                axios(config)
                        .then(response => {
                            this.getTokens();
                        });
            }
        }
    }
</script>
