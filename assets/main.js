String.prototype.format= function(replacements) {
  return this.replace(String.prototype.format.pattern, function(all, name) {
      return name in replacements? replacements[name] : all;
  });
}

String.prototype.format.pattern= /{?{([^{}]+)}}?/g;

var defaultParams = {
  title: location.hostname + " says",
  message: {
    show: true,
    content: ""
  },
  textbox: {
    show: false,
    default: null,
    password: false
  }
}

function build(params) {
  var config = Object.assign(defaultParams, params);
  console.log(config);

  var dialog = document.createElement("div");
  dialog.className = "popup";



  return dialog;
}

function prompt(prompt, defaultValue) {
  if(defaultValue == null) {
    defaultValue = "";
  }

  var popup = build({
    message: {
      content: prompt
    },
    textbox: {
      show: true,
      default: defaultValue,
      password: false
    },
  });
}