export default function (plop) {
  plop.setGenerator('component', {
    description: 'Tạo một Vue Component mới',
    prompts: [{
      type: 'input',
      name: 'name',
      message: 'Tên Component (VD: PlayerBar)?'
    }],
    actions: [{
      type: 'add',
      path: 'src/components/{{pascalCase name}}.vue',
      templateFile: 'plop-templates/Component.vue.hbs'
    }]
  });

  plop.setGenerator('view', {
    description: 'Tạo một Trang View mới',
    prompts: [{
      type: 'input',
      name: 'name',
      message: 'Tên View (VD: Explore)?'
    }],
    actions: [{
      type: 'add',
      path: 'src/views/{{pascalCase name}}View.vue',
      templateFile: 'plop-templates/Component.vue.hbs'
    }]
  });
};
