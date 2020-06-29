<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div v-if="tokens.length > 0">
            <div class="card card-default">
                <div class="card-header">Authorized Applications</div>

                <div class="card-body">
                    <!-- Authorized Tokens -->
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Scopes</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="token in tokens">
                                <!-- Client Name -->
                                <td style="vertical-align: middle;">
                                    {{ token.client.name }}
                                </td>

                                <!-- Scopes -->
                                <td style="vertical-align: middle;">
                                    <span v-if="token.scopes.length > 0">
                                        {{ token.scopes.join(', ') }}
                                    </span>
                                </td>

                                <!-- Revoke Button -->
                                <td style="vertical-align: middle;">
                                    <a class="action-link text-danger" @click="revoke(token)">
                                        Revoke
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                tokens: []
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
             * Prepare the component (Vue 2.x).
             */
            prepareComponent() {
                this.getTokens();
            },

            /**
             * Get all of the authorized tokens for the user.
             */
            getTokens() {
                var config = {
                    method: 'get',
                    url: 'oauth/tokens',
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
             * Revoke the given token.
             */
            revoke(token) {
                var config = {
                    method: 'delete',
                    url: 'oauth/tokens/' +  token.id,
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
