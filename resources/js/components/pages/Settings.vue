<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Tags</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="row step">
                                    <div class="col">
                                        <div class="form_title">
                                            <h3>
                                                1
                                            </h3>
                                        </div>
                                        <div class="middle-element">
                                            <div class="row">
                                                <div class="col">
                                                    <h4>Crea tus tags</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <vue-tags-input v-model="tag" :add-on-key="[13, ':', ';', ',']" :tags="tags" @before-adding-tag="addTag" @before-deleting-tag="deleteTag"
                                                                    @tags-changed="newTags => tags = newTags" placeholder="" class="tag-input"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row step">
                                    <div class="col">
                                        <div class="form_title">
                                            <h3>
                                                2
                                            </h3>
                                        </div>
                                        <div class="last-element">
                                            <div class="row">
                                                <div class="col">
                                                    <h4>Asigna palabras o expresiones regulares a tus tags</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <select class="form-control" @change="selectedTagChanged()" v-model="selectedTag">
                                                        <option value="1" :selected="!selectedTag" disabled>Selecciona un tag</option>
                                                        <option v-for="(tag, i) in tags" :value="tag" :key="i">{{ tag.text }}</option>
                                                    </select>

                                                    <div v-show="selectedTag" class="row words-row">
                                                        <div class="col">
                                                            <h5>Palabras</h5>
                                                            <vue-tags-input v-model="word" :add-on-key="[13, ':', ';', ',']" :tags="words"
                                                                            @tags-changed="newWords => updateWords(newWords)" placeholder="" class="tag-input"/>
                                                        </div>
                                                        <div class="col">
                                                            <h5>Expresiones regulares</h5>
                                                            <vue-tags-input v-model="regex" :add-on-key="[13, ':', ';', ',']" :tags="regexes"
                                                                            @tags-changed="newRegexes => updateRegexes(newRegexes)" placeholder="" class="tag-input"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueTagsInput from '@johmun/vue-tags-input';

    export default {
        props: [
            'user'
        ],
        components: {
            VueTagsInput,
        },
        mounted() {
            $('.ti-input').each(function (i) {
                $(this).attr('style', 'padding: 5px!important; border-radius: 3px!important; border-color: #aeaeaf!important; box-shadow: 0 0 5px rgba(38, 38, 76, 0.05)!important; min-height: 45px!important;');
            });
            this.getTags();
        },
        data() {
            return {
                tag: '',
                tags: [],
                word: '',
                words: [],
                regex: '',
                regexes: [],
                selectedTag: null,
                selectedTagIndex: null
            };
        },
        methods: {
            selectedTagChanged() {
                this.selectedTagIndex = this.tags.indexOf(this.selectedTag);

                let newWords = [];
                let newRegexes = [];

                if (this.user.current_user_profile.tags[this.selectedTagIndex] && this.user.current_user_profile.tags[this.selectedTagIndex].words) {
                    this.user.current_user_profile.tags[this.selectedTagIndex].words.slice(1, -1).split("|").forEach((word, index) => {
                        newWords.push({
                            text: word,
                            tiClasses: "ti-valid"
                        });
                    });
                }

                if (this.user.current_user_profile.tags[this.selectedTagIndex] && this.user.current_user_profile.tags[this.selectedTagIndex].regexes) {
                    this.user.current_user_profile.tags[this.selectedTagIndex].regexes.slice(1, -1).split("|").forEach((word, index) => {
                        newRegexes.push({
                            text: word,
                            tiClasses: "ti-valid"
                        });
                    });
                }

                this.words = newWords;
                this.regexes = newRegexes;
            },
            addTag(obj) {
                obj.addTag();

                axios.post('/ajax/profile/tags/add', {
                    user_profile_id: this.user.current_user_profile.id,
                    tag: obj.tag.text
                });
            },
            deleteTag(obj) {
                axios.post('/ajax/profile/tags/delete', {
                    user_profile_id: this.user.current_user_profile.id,
                    tag: obj.tag.text
                });

                obj.deleteTag();
                this.words = [];
            },
            updateWords(newWords) {
                this.words = newWords;

                axios.post('/ajax/profile/tags/words/update', {
                    user_profile_id: this.user.current_user_profile.id,
                    tag: this.selectedTag.text,
                    words: this.words.map(function (item) {
                        return item["text"];
                    })
                });
            },
            updateRegexes(newRegexes) {
                this.regexes = newRegexes;

                axios.post('/ajax/profile/tags/regexes/update', {
                    user_profile_id: this.user.current_user_profile.id,
                    tag: this.selectedTag.text,
                    regexes: this.regexes.map(function (item) {
                        return item["text"];
                    })
                });
            },
            getTags() {
                this.user.current_user_profile.tags.forEach((item, index) => {
                    this.tags.push({
                        text: item.tag,
                        tiClasses: "ti-valid"
                    });
                });
            },
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .tag-input {
        max-width: unset;
    }

    .step {
        .words-row {
            margin-top: 1em;
        }
    }

    .form_title {
        position: relative!important;

        h3 {
            margin: 0!important;
            padding: 0!important;

            background-color: $primaryColor!important;
            text-align: center!important;
            width: 40px!important;
            height: 40px!important;
            display: inline-block!important;
            -webkit-border-radius: 50%!important;
            -moz-border-radius: 50%!important;
            border-radius: 50%!important;
            color: #fff!important;
            font-size: 18px!important;
            line-height: 40px!important;
            position: absolute!important;
            left: 0!important;
            top: 0!important;
        }
    }

    .middle-element {
        border-left: 3px solid $primaryColor!important;
        padding-left: 40px;
        margin-left: 1.2rem;

        > .row:last-child {
            padding-bottom: 2.5em;
        }
    }

    .last-element {
        border: none!important;
        padding-left: 40px;
        margin-left: 1.2rem;
    }

    h4 {
        display: flex;
        align-items: center;

        .step-number {
            background-color: #7642FF;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            margin-right: 10px;
        }
    }

    @media (max-width: 576px) {
        .step {
            .words-row {
                > .col {
                    &:not(:first-child) {
                        margin-top: 15px;
                    }
                }
            }
        }
    }
</style>
