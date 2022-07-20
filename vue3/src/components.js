import ElementPlus from 'element-plus'
import * as ElementIcons from '@element-plus/icons'
import zhCn from 'element-plus/es/locale/lang/zh-cn'
import store from './store';


import ComponentRender from './components/ComponentRender';
import ModalProvider from './components/Modal/modal-provider.vue';
// import ModalDialog from './components/modal/modal-dialog.vue';
// import ModalDrawer from './components/modal/modal-drawer.vue';
import Layout from './layout/index.vue';
import QuickPage from './views/quick-page.vue';
import SvgIcon from './components/SvgIcon/index.vue';

import PageMain from './components/PageMain/index.vue';
import PageHeader from './components/PageHeader/index.vue';
import QuickAction from './components/QuickAction.vue';
import ActionGroup from './components/ActionGroup.vue';
import QuickDialog from './components/QuickDialog.vue';
import QuickDrawer from './components/QuickDrawer.vue';
import ShowHtml from './components/ShowHtml.vue';
import QuickTabs from './components/Tabs/QuickTabs.vue';
import QuickTabsPane from './components/Tabs/QuickTabsPane.vue';
import SelectIcon from './components/SelectIcon';
import QuickPopover from './components/QuickPopover';
import QuickPopconfirm from './components/QuickPopconfirm';
import InlineEdit from './components/InlineEdit';
import ImageUpload from './components/ImageUpload';
import QkTree from './components/QkTree/qk-tree';
import QkTreeItem from './components/QkTree/qk-tree-item';
import QkBadge from './components/Badge/index';
import IconCard from './components/Card/IconCard';
import InputItem from './components/Form/InputItem';
import QkDescriptions from './components/QkDescriptions';
import EditorQuill from './components/EditorQuill';
import QuickImages from './components/Attachment/quick-images';

// form
import FormRender from './components/Form/FormRender.vue';
import QuickForm from './components/Form/QuickForm.vue';
import FilterForm from './components/Form/FilterForm.vue';
import FooterField from './components/Form/Fields/FooterField.vue';
import DefaultField from './components/Form/Fields/DefaultField.vue';
import TextField from './components/Form/Fields/TextField.vue';
import SelectField from './components/Form/Fields/SelectField.vue';
import InputNumberField from './components/Form/Fields/InputNumberField.vue';
import ColorField from './components/Form/Fields/ColorField.vue';
import SwitchField from './components/Form/Fields/SwitchField.vue';
import TimeField from './components/Form/Fields/TimeField.vue';
import RateField from './components/Form/Fields/RateField.vue';
import RadioField from './components/Form/Fields/RadioField.vue';
import CheckboxField from './components/Form/Fields/CheckboxField.vue';
import SliderField from './components/Form/Fields/SliderField.vue';
import TransferField from './components/Form/Fields/TransferField.vue';
import IconField from './components/Form/Fields/IconField.vue';
import CascaderField from './components/Form/Fields/CascaderField.vue';
import DateField from './components/Form/Fields/DateField.vue';
import UploadField from './components/Form/Fields/UploadField.vue';
import JsonField from './components/Form/Fields/JsonField.vue';
import DynamicField from './components/Form/Fields/DynamicField.vue';
import TreeField from './components/Form/Fields/TreeField.vue';
import Tree2Field from './components/Form/Fields/Tree2Field.vue';
import ImagesField from './components/Form/Fields/ImagesField.vue';
import QuillField from './components/Form/Fields/QuillField.vue';
import SelectTagField from './components/Form/Fields/SelectTagField.vue';
import WhenField from './components/Form/Fields/WhenField.vue';


import TablePanel from './components/Table/TablePanel.vue';
import QuickTable from './components/Table/QuickTable.vue';
import QuickColumn from './components/Table/QuickColumn.vue';
import IndexActionField from './components/Table/Fields/ActionField.vue';
import IndexTagField from './components/Table/Fields/TagField.vue';
import IndexImageField from './components/Table/Fields/ImageField.vue';
import ShowField from './components/Table/Fields/ShowField.vue';
import IndexSwitchField from './components/Table/Fields/SwitchField.vue';




import QkImage from './components/Show/QkImage.vue';




export default {
  install: (app) => {


    app.use(ElementPlus, {
      locale: zhCn,
      size: store.state.settings.elementSize
    })
    for (var key in ElementIcons) {
      app.component(`ElIcon${ElementIcons[key].name}`, ElementIcons[key])
    }


    app.component('page-main', PageMain);
    app.component('quick-icon', SvgIcon);
    app.component('modal-provider', ModalProvider);
    // app.component('modal-drawer', ModalDrawer);
    // app.component('modal-dialog', QuickDialog);
    app.component('page-header', PageHeader);
    app.component('quick-action', QuickAction);
    app.component('action-group', ActionGroup);
    app.component('quick-dialog', QuickDialog);
    app.component('quick-drawer', QuickDrawer);
    app.component('show-html', ShowHtml);
    app.component('image-upload', ImageUpload);

    // app.component('json-render', RenderComponent);
    app.component('json-render', ComponentRender);
    app.component('qk-descriptions', QkDescriptions);


    app.component('quick-tabs-pane', QuickTabsPane);
    app.component('quick-tabs', QuickTabs);
    app.component('select-icon', SelectIcon);
    app.component('quick-popover', QuickPopover);
    app.component('quick-popconfirm', QuickPopconfirm);
    app.component('inline-edit', InlineEdit);
    app.component('editor-quill', EditorQuill);


    app.component('quick-layout', Layout);
    app.component('quick-page', QuickPage);
    app.component('svg-icon', SvgIcon);
    app.component('qk-tree', QkTree);
    app.component('qk-tree-item', QkTreeItem);
    app.component('quick-badge', QkBadge);
    app.component('icon-card', IconCard);
    app.component('quick-attachment', QuickImages);


    // form
    app.component('form-render', FormRender);
    app.component('quick-form', QuickForm);
    app.component('filter-form', FilterForm);
    app.component('form-footer-field', FooterField);
    app.component('form-text-field', TextField);
    app.component('default-field', DefaultField);
    app.component('form-select-field', SelectField);
    app.component('form-input-number-field', InputNumberField);
    app.component('form-color-field', ColorField);
    app.component('form-switch-field', SwitchField);
    app.component('form-time-field', TimeField);
    app.component('form-rate-field', RateField);
    app.component('form-radio-field', RadioField);
    app.component('form-checkbox-field', CheckboxField);
    app.component('form-slider-field', SliderField);
    app.component('form-transfer-field', TransferField);
    app.component('form-icon-field', IconField);
    app.component('form-cascader-field', CascaderField);
    app.component('form-date-field', DateField);
    app.component('form-upload-field', UploadField);
    app.component('form-json-field', JsonField);
    app.component('form-dynamic-field', DynamicField);
    app.component('form-tree-field', TreeField);
    app.component('form-tree2-field', Tree2Field);
    app.component('form-images-field', ImagesField);
    app.component('form-quill-field', QuillField);
    app.component('form-select-tag-field', SelectTagField);
    app.component('form-when-field', WhenField);







    app.component('table-panel',TablePanel)
    app.component('quick-table',QuickTable)
    app.component('quick-column',QuickColumn)
    app.component('index-switch-field', IndexSwitchField)
    app.component('index-tag-field', IndexTagField)
    app.component('index-image-field', IndexImageField)
    app.component('index-action-field', IndexActionField)
    app.component('index-show-field', ShowField)
    app.component('qk-image', QkImage)
    app.component('input-item', InputItem)





  },
};
