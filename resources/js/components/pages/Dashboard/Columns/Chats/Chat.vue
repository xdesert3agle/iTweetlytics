<template>
    <div class="row no-gutters">
        <div class="col chat-container">
            <div class="row no-gutters chat-title-row">
                <div class="col-auto back-button-container">
                    <i class="fas fa-chevron-left" @click="$emit('back')"></i>
                </div>
                <div class="col">
                    <span class="chat-title">{{ updatedChat.user.name }}</span>
                    <span class="chat-subtitle text-muted">@{{ updatedChat.user.screen_name }}</span>
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col">
                    <div class="chat">
                        <div class="message-list-container">
                            <div v-for="(message, i) in reversedMessageList"
                                 :class="{ 'mine': message.message_create.sender_id == userId, 'yours': message.message_create.sender_id != userId }"
                                 class="messages">
                                <span class="message last">{{ message.message_create.message_data.text }}</span>
                            </div>
                        </div>

                        <div class="row no-gutters send-message-input-container">
                            <div class="col">
                                <input v-model="text" type="text" class="form-control" placeholder="Escribe un mensaje">
                            </div>
                            <div class="col-auto">
                                <i @click="sendMessage" class="fa fa-lg fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'chat',
            'userId'
        ],
        data () {
            return {
                updatedChat: this.chat,
                text: null
            }
        },
        computed : {
            reversedMessageList() {
                return this.updatedChat.messages.slice().reverse()
            }
        },
        methods: {
            sendMessage() {
                axios.post('/ajax/dm/send', {
                    'recipientId': this.updatedChat.user.id_str,
                    'text': this.text
                }).then((response) => {
                    console.log(response.data);
                    this.updatedChat.messages.push(response.data.event);
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;

    .chat-container {
        .chat-title-row {
            padding: 10px;

            .back-button-container {
                display: flex;
                align-items: center;
                margin-right: 15px;

                i {
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    width: 26px;
                    height: 26px;

                    cursor: pointer;
                    transition: 250ms;
                    border-radius: 50%;

                    padding-right: 4px;

                    &:hover {
                        background-color: lighten($primaryColor, 30%);
                    }
                }
            }
        }

        .chat-title {
            display: block;
            margin-bottom: 0 !important;

            font-size: 14pt;
            font-weight: 500;

            line-height: initial;
        }

        .chat-subtitle {
            font-size: 11pt;
        }

        .chat {
            display: flex;
            flex-direction: column;

            height: calc(100vh - 72px - 15px - 15px - 48px - 105px);

            .message-list-container {
                overflow-y: scroll;
                overflow-x: hidden;
                padding: 15px;

                .messages {
                    display: flex;
                    flex-direction: column;
                }

                .message {
                    border-radius: 20px;
                    padding: 8px 15px;
                    margin-top: 5px;
                    margin-bottom: 5px;
                    display: inline-block;
                }

                .yours {
                    align-items: flex-start;
                }

                .yours .message {
                    margin-right: 25%;
                    background-color: #eee;
                    position: relative;
                }

                .yours .message.last:before {
                    content: "";
                    position: absolute;
                    z-index: 0;
                    bottom: 0;
                    left: -7px;
                    height: 20px;
                    width: 20px;
                    background: #eee;
                    border-bottom-right-radius: 15px;
                }

                .yours .message.last:after {
                    content: "";
                    position: absolute;
                    z-index: 1;
                    bottom: 0;
                    left: -10px;
                    width: 10px;
                    height: 20px;
                    background: white;
                    border-bottom-right-radius: 10px;
                }

                .mine {
                    align-items: flex-end;
                }

                .mine .message {
                    color: white;
                    margin-left: 25%;
                    background: linear-gradient(to bottom, #00D0EA 0%, #0085D1 100%);
                    background-attachment: fixed;
                    position: relative;
                }

                .mine .message.last:before {
                    content: "";
                    position: absolute;
                    z-index: 0;
                    bottom: 0;
                    right: -8px;
                    height: 20px;
                    width: 20px;
                    background: linear-gradient(to bottom, #00D0EA 0%, #0085D1 100%);
                    background-attachment: fixed;
                    border-bottom-left-radius: 15px;
                }

                .mine .message.last:after {
                    content: "";
                    position: absolute;
                    z-index: 1;
                    bottom: 0;
                    right: -10px;
                    width: 10px;
                    height: 20px;
                    background: white;
                    border-bottom-left-radius: 10px;
                }
            }

            .send-message-input-container {
                padding: 15px;
                border: 1px solid rgba(0, 0, 0, 0.125);

                input {
                    width: 100%;
                }

                i {
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    padding: 15px 0 15px 15px;

                    cursor: pointer;
                }
            }
        }
    }
</style>
