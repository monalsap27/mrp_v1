<template>
  <el-card v-if="user.name">
    <el-tabs v-model="activeActivity" @tab-click="handleClick">
      <el-tab-pane label="Activity" name="first">
        <div class="user-activity">
          <div class="post">
            <div class="user-block">
              <img
                class="img-circle"
                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDkaQO69Fro8SZLTVZQ75JH2R0T-sn5yIA_lKGwvvgQ0R0BoQtUQ"
                alt="user image"
              >
              <span class="username text-muted">
                <a href="#">Iron Man</a>
                <a href="#" class="pull-right btn-box-tool">
                  <i class="fa fa-times" />
                </a>
              </span>
              <span class="description">Shared publicly - 7:30 PM today</span>
            </div>
            <p>
              Lorem ipsum represents a long-held tradition for designers,
              typographers and the like. Some people hate it and argue for its
              demise, but others ignore the hate as they create awesome tools to
              help create filler text for everyone from bacon lovers to Charlie
              Sheen fans.
            </p>
            <ul class="list-inline">
              <li>
                <a href="#" class="link-black text-sm">
                  <i class="el-icon-share" /> Share
                </a>
              </li>
              <li>
                <a href="#" class="link-black text-sm">
                  <svg-icon icon-class="like" />Like
                </a>
              </li>
              <li class="pull-right">
                <a href="#" class="link-black text-sm">
                  <svg-icon icon-class="comment" />Comments (5)
                </a>
              </li>
            </ul>
            <el-input placeholder="Type a comment" />
          </div>
          <div class="post">
            <div class="user-block">
              <img
                class="img-circle"
                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMMN-8f9CQQ3MKJpboBJIqaiJ2Wus2Tf4w_vx9STtalxrY3qGJ"
                alt="user image"
              >
              <span class="username text-muted">
                <a href="#">Captain American</a>
                <a href="#" class="pull-right btn-box-tool">
                  <i class="fa fa-times" />
                </a>
              </span>
              <span class="description">Sent you a message - yesterday</span>
            </div>
            <p>
              Lorem ipsum represents a long-held tradition for designers,
              typographers and the like. Some people hate it and argue for its
              demise, but others ignore the hate as they create awesome tools to
              help create filler text for everyone from bacon lovers to Charlie
              Sheen fans.
            </p>
            <el-input placeholder="Response">
              <el-button slot="append"> Send </el-button>
            </el-input>
          </div>
          <div class="post">
            <div class="user-block">
              <img
                class="img-circle img-bordered-sm"
                src="/svg/logoS.png"
                alt="User Image"
              >
              <span class="username">
                <a href="#">Daredevil</a>
                <a href="#" class="pull-right btn-box-tool">
                  <i class="fa fa-times" />
                </a>
              </span>
              <span class="description">Posted 4 photos - 2 days ago</span>
            </div>
            <div class="user-images">
              <el-carousel :interval="6000" type="card" height="200px">
                <el-carousel-item v-for="item in carouselImages" :key="item">
                  <img :src="item" class="image">
                </el-carousel-item>
              </el-carousel>
            </div>
            <ul class="list-inline">
              <li>
                <a href="#" class="link-black text-sm">
                  <i class="el-icon-share" /> Share
                </a>
              </li>
              <li>
                <a href="#" class="link-black text-sm">
                  <svg-icon icon-class="like" />Like
                </a>
              </li>
              <li class="pull-right">
                <a href="#" class="link-black text-sm">
                  <svg-icon icon-class="comment" />Comments (5)
                </a>
              </li>
            </ul>
            <el-input placeholder="Type a comment" />
          </div>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Timeline" name="second">
        <div class="block">
          <el-timeline>
            <el-timeline-item timestamp="2019/4/17" placement="top">
              <el-card>
                <h4>Update Github template</h4>
                <p>tuandm committed 2019/4/17 20:46</p>
              </el-card>
            </el-timeline-item>
            <el-timeline-item timestamp="2019/4/18" placement="top">
              <el-card>
                <h4>Update Github template</h4>
                <p>tonynguyen committed 2019/4/18 20:46</p>
              </el-card>
              <el-card>
                <h4>Update Github template</h4>
                <p>tuandm committed 2019/4/19 21:16</p>
              </el-card>
            </el-timeline-item>
            <el-timeline-item timestamp="2019/4/19" placement="top">
              <el-card>
                <h4>
                  Deploy
                  <a href="https://laravue.dev" target="_blank">laravue.dev</a>
                </h4>
                <p>tuandm deployed 2019/4/19 10:23</p>
              </el-card>
            </el-timeline-item>
          </el-timeline>
        </div>
      </el-tab-pane>
      <el-tab-pane v-loading="updating" label="Account" name="third">
        <el-form-item label="Name">
          <el-input v-model="user.name" :disabled="user.role === 'admin'" />
        </el-form-item>
        <el-form-item label="Email">
          <el-input v-model="user.email" :disabled="user.role === 'admin'" />
        </el-form-item>
        <el-form-item label="Signature">
          <el-upload
            class="upload-demo"
            action=""
            :http-request="uploadFiles"
            :on-preview="handlePreview"
            :on-remove="handleRemove"
            :file-list="fileList"
            list-type="picture"
          >
            <el-button slot="trigger" size="small" type="primary">Click to upload</el-button>
            <el-button
              size="small"
              type="primary"
              @click="centerDialogVisible = true"
            >Click to new</el-button>
            <div slot="tip" class="el-upload__tip">
              jpg/png files with a size less than 500kb
            </div>
          </el-upload>
        </el-form-item>
        <el-form-item>
          <el-button
            type="primary"
            :disabled="user.role === 'admin'"
            @click="onSubmit"
          >
            Update
          </el-button>
        </el-form-item>
      </el-tab-pane>
    </el-tabs>
    <el-dialog
      title="Signature"
      :visible.sync="centerDialogVisible"
      width="30%"
      center
    >
      <div class="container">
        <VueSignaturePad
          id="signature"
          ref="signaturePad"
          width="100%"
          height="300px"
          :options="options"
        />
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="danger" @click="centerDialogVisible = false">Cancel</el-button>
        <el-button type="info" @click="undo">Undo</el-button>
        <el-button type="success" @click="save">Confirm</el-button>
      </span>
    </el-dialog>
  </el-card>
</template>

<script>
import Resource from '@/api/resource';
const userResource = new Resource('users');
import { uploadSignature, ShowSignature } from '@/api/user_resource';

export default {
  props: {
    user: {
      type: Object,
      default: () => {
        return {
          name: '',
          email: '',
          avatar: '',
          roles: [],
        };
      },
    },
  },
  data() {
    return {
      centerDialogVisible: false,
      activeActivity: 'first',
      carouselImages: [
        'https://cdn.laravue.dev/photo1.png',
        'https://fuss10.elemecdn.com/3/63/4e7f3a15429bfda99bce42a18cdd1jpeg.jpeg?imageMogr2/thumbnail/360x360/format/webp/quality/100',
        '/svg/logoS.png',
        'https://cdn.laravue.dev/photo4.jpg',
      ],
      updating: false,
      options: { penColor: '#c0f' },
      fileList: [],
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchData(id);
  },
  methods: {
    handleRemove(file, fileList) {
      console.log(file, fileList);
    },
    handlePreview(file) {
      console.log(file);
    },
    fetchData(id) {
      ShowSignature(id)
        .then((response) => {
          this.fileList.push({
            name: response.data.signature_file,
            url: 'storage/signature/' + response.data.signature_file,
          });
        })
        .catch((err) => {
          console.log(err);
        });
    },
    uploadFiles(file) {
      const fd = new FormData();
      fd.append('file', file.file);
      fd.append('id', this.user.id);
      fd.append('isImage', 'true');
      uploadSignature(fd)
        .then((response) => {
          this.$message({
            message:
              'New categories ' +
              this.newCategories.name +
              ' has been created successfully.',
            type: 'success',
            duration: 5 * 1000,
          });
          this.$router.go(this.$router.currentRoute);
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.categoriesCreating = false;
        });
    },
    handleClick(tab, event) {
      console.log('Switching tab ', tab, event);
    },
    onSubmit() {
      this.updating = true;
      userResource
        .update(this.user.id, this.user)
        .then((response) => {
          this.updating = false;
          this.$message({
            message: 'User information has been updated successfully',
            type: 'success',
            duration: 5 * 1000,
          });
        })
        .catch((error) => {
          console.log(error);
          this.updating = false;
        });
    },
    undo() {
      this.$refs.signaturePad.undoSignature();
    },
    save() {
      const { data } = this.$refs.signaturePad.saveSignature();
      const fd = new FormData();
      fd.append('file', data);
      fd.append('id', this.user.id);
      fd.append('isImage', 'false');
      uploadSignature(fd)
        .then((response) => {
          console.log(response);
          this.$message({
            message: response.message,
            type: 'success',
            duration: 5 * 1000,
          });
          this.centerDialogVisible = false;
          this.$router.go(this.$router.currentRoute);
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.categoriesCreating = false;
        });
    },
    change() {
      this.options = {
        penColor: '#00f',
      };
    },
    resume() {
      this.options = {
        penColor: '#c0f',
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.user-activity {
  .user-block {
    .username,
    .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }
    img {
      width: 40px;
      height: 40px;
      float: left;
    }
    :after {
      clear: both;
    }
    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }
    span {
      font-weight: 500;
      font-size: 12px;
    }
  }
  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
    .image {
      width: 100%;
    }
    .user-images {
      padding-top: 20px;
    }
  }
  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;
    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }
    .link-black {
      &:hover,
      &:focus {
        color: #999;
      }
    }
  }
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }

  .el-carousel__item:nth-child(2n + 1) {
    background-color: #d3dce6;
  }
}
#signature {
  border: double 3px transparent;
  border-radius: 5px;
  background-image: linear-gradient(white, white),
    radial-gradient(circle at top left, #4bc5e8, #9f6274);
  background-origin: border-box;
  background-clip: content-box, border-box;
}
</style>
