<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Ajustes</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <div class="row step">
                                    <div class="col-5">
                                        <h4>1. Crea tus tags</h4>
                                        <vue-tags-input v-model="tag" :add-on-key="[13, ':', ';', ',']" :tags="tags" @before-adding-tag="addTag" @before-deleting-tag="deleteTag" @tags-changed="newTags => tags = newTags" placeholder="" class="tag-input" />
                                    </div>
                                </div>

                                <div class="row step">
                                    <div class="col-5">
                                        <h4>2. Asigna palabras a tus tags</h4>
                                        <div class="row">
                                            <div class="col-7">
                                                <select class="form-control" @change="selectedTagChanged()" v-model="selectedTag">
                                                    <option value="1" :selected="!selectedTag" disabled>Selecciona un tag</option>
                                                    <option v-for="(tag, i) in tags" :value="tag" :key="i">{{ tag.text }}</option>
                                                </select>
                                            </div>
                                            <div v-if="selectedTag" class="col">
                                                <vue-tags-input v-model="word" :add-on-key="[13, ':', ';', ',']" :tags="words" @tags-changed="newWords => updateWords(newWords)" placeholder="" class="tag-input" />
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
            $('.ti-input').attr('style', 'padding: 5px!important; border-radius: 3px!important; border-color: #aeaeaf!important; box-shadow: 0 0 5px rgba(38, 38, 76, 0.05)!important;');
            this.getTags();
        },
        data() {
            return {
                tag: '',
                tags: [],
                word: '',
                words: [],
                selectedTag: null,
                selectedTagIndex: null
            };
        },
        methods: {
            selectedTagChanged() {
                this.selectedTagIndex = this.tags.indexOf(this.selectedTag);

                let newWords = [];

                if (this.user.current_twitter_profile[0].tags[this.selectedTagIndex] && this.user.current_twitter_profile[0].tags[this.selectedTagIndex].words) {
                    this.user.current_twitter_profile[0].tags[this.selectedTagIndex].words.split(",").forEach((word, index) => {
                        newWords.push({
                            text: word,
                            tiClasses: "ti-valid"
                        });
                    });
                }

                this.words = newWords;
            },
            addTag(obj) {
                obj.addTag();

                axios.post('/ajax/profile/tags/add', {
                    twitter_profile_id: this.user.current_twitter_profile[0].id,
                    tag: obj.tag.text
                });
            },
            deleteTag(obj) {
                console.log(obj.tag.text);

                axios.post('/ajax/profile/tags/delete', {
                    twitter_profile_id: this.user.current_twitter_profile[0].id,
                    tag: obj.tag.text
                });

                obj.deleteTag();
                this.words = [];
            },
            updateWords(newWords) {
                this.words = newWords;

                axios.post('/ajax/profile/tags/words/update', {
                    twitter_profile_id: this.user.current_twitter_profile[0].id,
                    tag: this.selectedTag.text,
                    words: this.words.map(function(item) { return item["text"]; })
                });
            },
            getTags() {
                this.user.current_twitter_profile[0].tags.forEach((item, index) => {
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
        &:not(:first-child) {
            margin-top: 2em;
        }
    }

    .card {

    }
</style>
