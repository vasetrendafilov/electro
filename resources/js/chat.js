
let userPubKey = '' || 'pub-c-eac6e378-83b2-44ee-92b2-6e779f798c72';
let userSubKey = '' || 'sub-c-865b0ece-af02-11e8-bf00-aaab7b0b8683';

jQuery.fn.sortDomElements = (function() {
  return function(comparator) {
    return Array.prototype.sort.call(this, comparator).each(function(i) {
      this.parentNode.appendChild(this);
    });
  };
})();
var generatePerson = function(online) {
  var person = {};
  person.first = $('#username').val();
  person.uuid = $('#id').val();
  person.online = online || false;
  return person;
}
var app = {
    messageToSend: '',
    ChatEngine: false,
    me: false,
    chat: false,
    users: [],
    messages: [],
    init: function() {
      this.ChatEngine = ChatEngineCore.create({
        publishKey: userPubKey,
        subscribeKey: userSubKey
      });
      let newPerson = generatePerson(true);
      chatEngine = this.ChatEngine;
      this.ChatEngine.connect(newPerson.uuid, newPerson);
      this.cacheDOM();
      this.ChatEngine.on('$.ready', function(data) {
        app.ready(data);
        app.bindMessages();
      });
    },
    ready: function(data) {
      this.me = data.me;
      this.chat = new this.ChatEngine.Chat($('#chat-room').val());
      const emoji = ChatEngineCore.plugin['chat-engine-emoji']();
      this.chat.plugin(emoji);
      this.chat.on('$.connected', () => {
         $('#chat-loading').fadeOut();
         this.chat.search({
             'reverse': true,
             event: 'message',
             limit: 50
         }).on('message', (data) => {
           app.renderMessage(data, true);
         }).plugin(emoji);
       });
      this.bindEvents();
    },
    cacheDOM: function() {
      this.$chatHeaderBtn = $('#close-chat');
      this.$chatHistory = $('.chat-history');
      this.$button = $('#send');
      this.$textarea = $('#message-to-send');
      this.$chatHistoryList = this.$chatHistory.find('ul');
    },
    bindEvents: function() {
      this.$button.on('click', this.sendMessage.bind(this));
      this.$chatHeaderBtn.on('click', this.removeChat.bind(this));
      this.$textarea.on('keyup', this.sendMessageEnter.bind(this));
    },
    bindMessages: function() {
      this.chat.on('message', function(message) {
        app.renderMessage(message);
      });
    },
    renderMessage: function(message) {
      var meTemp = Handlebars.compile($("#message-template").html());
      var userTemp = Handlebars.compile($("#message-response-template").html());
      var template = userTemp;
      if (message.sender.uuid == app.me.uuid) {
        template = meTemp;
      }
      var messageJsTime = new Date(parseInt(message.timetoken.substring(0,13)));
      var context = {
        messageOutput: message.data.text,
        tt: messageJsTime.getTime(),
        time: app.parseTime(messageJsTime),
        user: message.sender.state
      };
      app.$chatHistoryList.append(template(context));
      app.$chatHistoryList
      .children()
      .sortDomElements(function(a,b){
          akey = a.dataset.order;
          bkey = b.dataset.order;
          if (akey == bkey) return 0;
          if (akey < bkey) return -1;
          if (akey > bkey) return 1;
      });
      $('.emoji').css('height', '26px');
      $('.emoji').parents('.message').css({"padding-top": "9px","padding-bottom": "9px"});
      this.scrollToBottom();
    },
    sendMessage: function() {
      this.messageToSend = this.$textarea.val()
      if (this.messageToSend.trim() !== '') {
        this.$textarea.val("");
        this.chat.emit('message', {
          text: this.messageToSend
        });
      }
    },
    removeChat: function() {
      this.ChatEngine.disconnect($('#chat-room').val());
    },
    sendMessageEnter: function(event) {
      if (event.keyCode === 13) {
        this.sendMessage();
        if (msgCount == 0) {
          var username = $('#friend-name').text();
          $.get("/mafija2/public/chat/add",{username:username});
          msgCount = 1;
        }
      }
    },
    scrollToBottom: function() {
      app.$chatHistory.scrollTop(10000000);
    },
    parseTime: function(time) {
      return time.toLocaleDateString() + ", " + time.toLocaleTimeString().
      replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
    },
    getCurrentTime: function() {
      return new Date().toLocaleTimeString().
      replace(/([\d]+:[\d]{2})(:[\d]{2})(.*)/, "$1$3");
    },
    getRandomItem: function(arr) {
      return arr[Math.floor(Math.random() * arr.length)];
    }
};
app.init();
