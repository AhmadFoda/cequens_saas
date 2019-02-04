(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["app"],{

/***/ "./web/assets/css/style.css":
/*!**********************************!*\
  !*** ./web/assets/css/style.css ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./web/assets/js/app.js":
/*!******************************!*\
  !*** ./web/assets/js/app.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

// assets/js/app.js
__webpack_require__(/*! ../css/style.css */ "./web/assets/css/style.css");

console.log('Hello Webpack Encore'); // ---------------------------------------------
// Chatkit Code
// ---------------------------------------------

var tokenProvider = new Chatkit.TokenProvider({
  url: "https://us1.pusherplatform.io/services/chatkit_token_provider/v1/bb957f41-bf24-4f23-a015-1f51ceafb1b2/token"
});
var chatManager = new Chatkit.ChatManager({
  instanceLocator: "v1:us1:bb957f41-bf24-4f23-a015-1f51ceafb1b2",
  userId: "k.mohamed@cequens.com",
  tokenProvider: tokenProvider
});
chatManager.connect().then(function (user) {
  setUser(user);
  user.subscribeToRoom({
    roomId: user.rooms[0].id,
    hooks: {
      onNewMessage: addMessage
    },
    messageLimit: 10
  }).then(setRoom);
}); // ---------------------------------------------
// Application Code
// ---------------------------------------------

var _hyperapp = hyperapp,
    app = _hyperapp.app,
    h = _hyperapp.h;
/* @jsx h */

var state = {
  user: {
    avatarURL: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
  },
  room: {},
  messages: []
};
var actions = {
  setUser: function setUser(user) {
    return {
      user: user
    };
  },
  setRoom: function setRoom(room) {
    return {
      room: room
    };
  },
  addMessage: function addMessage(payload) {
    return function (_ref) {
      var messages = _ref.messages;
      return {
        messages: [payload].concat(_toConsumableArray(messages))
      };
    };
  }
};

var _app = app(state, actions, view, document.body),
    addMessage = _app.addMessage,
    setUser = _app.setUser,
    setRoom = _app.setRoom;

/***/ })

},[["./web/assets/js/app.js","runtime"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi93ZWIvYXNzZXRzL2Nzcy9zdHlsZS5jc3MiLCJ3ZWJwYWNrOi8vLy4vd2ViL2Fzc2V0cy9qcy9hcHAuanMiXSwibmFtZXMiOlsicmVxdWlyZSIsImNvbnNvbGUiLCJsb2ciLCJ0b2tlblByb3ZpZGVyIiwiQ2hhdGtpdCIsIlRva2VuUHJvdmlkZXIiLCJ1cmwiLCJjaGF0TWFuYWdlciIsIkNoYXRNYW5hZ2VyIiwiaW5zdGFuY2VMb2NhdG9yIiwidXNlcklkIiwiY29ubmVjdCIsInRoZW4iLCJ1c2VyIiwic2V0VXNlciIsInN1YnNjcmliZVRvUm9vbSIsInJvb21JZCIsInJvb21zIiwiaWQiLCJob29rcyIsIm9uTmV3TWVzc2FnZSIsImFkZE1lc3NhZ2UiLCJtZXNzYWdlTGltaXQiLCJzZXRSb29tIiwiaHlwZXJhcHAiLCJhcHAiLCJoIiwic3RhdGUiLCJhdmF0YXJVUkwiLCJyb29tIiwibWVzc2FnZXMiLCJhY3Rpb25zIiwicGF5bG9hZCIsInZpZXciLCJkb2N1bWVudCIsImJvZHkiXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLHVDOzs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FDQUE7QUFFQUEsbUJBQU8sQ0FBQyxvREFBRCxDQUFQOztBQUVBQyxPQUFPLENBQUNDLEdBQVIsQ0FBWSxzQkFBWixFLENBRUE7QUFDQTtBQUNBOztBQUdBLElBQU1DLGFBQWEsR0FBRyxJQUFJQyxPQUFPLENBQUNDLGFBQVosQ0FBMEI7QUFDL0NDLEtBQUc7QUFENEMsQ0FBMUIsQ0FBdEI7QUFJQSxJQUFNQyxXQUFXLEdBQUcsSUFBSUgsT0FBTyxDQUFDSSxXQUFaLENBQXdCO0FBQzNDQyxpQkFBZSxFQUFFLDZDQUQwQjtBQUUzQ0MsUUFBTSxFQUFFLHVCQUZtQztBQUczQ1AsZUFBYSxFQUFiQTtBQUgyQyxDQUF4QixDQUFwQjtBQU1BSSxXQUFXLENBQUNJLE9BQVosR0FBc0JDLElBQXRCLENBQTJCLFVBQUFDLElBQUksRUFBSTtBQUNsQ0MsU0FBTyxDQUFDRCxJQUFELENBQVA7QUFDQUEsTUFBSSxDQUNGRSxlQURGLENBQ2tCO0FBQ2hCQyxVQUFNLEVBQUVILElBQUksQ0FBQ0ksS0FBTCxDQUFXLENBQVgsRUFBY0MsRUFETjtBQUVoQkMsU0FBSyxFQUFFO0FBQUVDLGtCQUFZLEVBQUVDO0FBQWhCLEtBRlM7QUFHaEJDLGdCQUFZLEVBQUU7QUFIRSxHQURsQixFQU1FVixJQU5GLENBTU9XLE9BTlA7QUFPQSxDQVRELEUsQ0FZQTtBQUNBO0FBQ0E7O2dCQUdtQkMsUTtJQUFYQyxHLGFBQUFBLEc7SUFBS0MsQyxhQUFBQSxDO0FBQ2I7O0FBRUEsSUFBTUMsS0FBSyxHQUFHO0FBQ2JkLE1BQUksRUFBRTtBQUNMZSxhQUFTLEVBQ1I7QUFGSSxHQURPO0FBS2JDLE1BQUksRUFBRSxFQUxPO0FBTWJDLFVBQVEsRUFBRTtBQU5HLENBQWQ7QUFTQSxJQUFNQyxPQUFPLEdBQUc7QUFDZmpCLFNBQU8sRUFBRSxpQkFBQUQsSUFBSTtBQUFBLFdBQUs7QUFBRUEsVUFBSSxFQUFKQTtBQUFGLEtBQUw7QUFBQSxHQURFO0FBRWZVLFNBQU8sRUFBRSxpQkFBQU0sSUFBSTtBQUFBLFdBQUs7QUFBRUEsVUFBSSxFQUFKQTtBQUFGLEtBQUw7QUFBQSxHQUZFO0FBR2ZSLFlBQVUsRUFBRSxvQkFBQVcsT0FBTztBQUFBLFdBQUk7QUFBQSxVQUFHRixRQUFILFFBQUdBLFFBQUg7QUFBQSxhQUFtQjtBQUN6Q0EsZ0JBQVEsR0FBR0UsT0FBSCw0QkFBZUYsUUFBZjtBQURpQyxPQUFuQjtBQUFBLEtBQUo7QUFBQTtBQUhKLENBQWhCOztXQVF5Q0wsR0FBRyxDQUMzQ0UsS0FEMkMsRUFFM0NJLE9BRjJDLEVBRzNDRSxJQUgyQyxFQUkzQ0MsUUFBUSxDQUFDQyxJQUprQyxDO0lBQXBDZCxVLFFBQUFBLFU7SUFBWVAsTyxRQUFBQSxPO0lBQVNTLE8sUUFBQUEsTyIsImZpbGUiOiJhcHAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW4iLCIvLyBhc3NldHMvanMvYXBwLmpzXG5cbnJlcXVpcmUoJy4uL2Nzcy9zdHlsZS5jc3MnKTtcblxuY29uc29sZS5sb2coJ0hlbGxvIFdlYnBhY2sgRW5jb3JlJyk7XG5cbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuLy8gQ2hhdGtpdCBDb2RlXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXG5jb25zdCB0b2tlblByb3ZpZGVyID0gbmV3IENoYXRraXQuVG9rZW5Qcm92aWRlcih7XG5cdHVybDogYGh0dHBzOi8vdXMxLnB1c2hlcnBsYXRmb3JtLmlvL3NlcnZpY2VzL2NoYXRraXRfdG9rZW5fcHJvdmlkZXIvdjEvYmI5NTdmNDEtYmYyNC00ZjIzLWEwMTUtMWY1MWNlYWZiMWIyL3Rva2VuYFxufSlcblxuY29uc3QgY2hhdE1hbmFnZXIgPSBuZXcgQ2hhdGtpdC5DaGF0TWFuYWdlcih7XG5cdGluc3RhbmNlTG9jYXRvcjogXCJ2MTp1czE6YmI5NTdmNDEtYmYyNC00ZjIzLWEwMTUtMWY1MWNlYWZiMWIyXCIsXG5cdHVzZXJJZDogXCJrLm1vaGFtZWRAY2VxdWVucy5jb21cIixcblx0dG9rZW5Qcm92aWRlclxufSlcblxuY2hhdE1hbmFnZXIuY29ubmVjdCgpLnRoZW4odXNlciA9PiB7XG5cdHNldFVzZXIodXNlcilcblx0dXNlclxuXHRcdC5zdWJzY3JpYmVUb1Jvb20oe1xuXHRcdFx0cm9vbUlkOiB1c2VyLnJvb21zWzBdLmlkLFxuXHRcdFx0aG9va3M6IHsgb25OZXdNZXNzYWdlOiBhZGRNZXNzYWdlIH0sXG5cdFx0XHRtZXNzYWdlTGltaXQ6IDEwXG5cdFx0fSlcblx0XHQudGhlbihzZXRSb29tKVxufSlcblxuXG4vLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbi8vIEFwcGxpY2F0aW9uIENvZGVcbi8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXG5cbmNvbnN0IHsgYXBwLCBoIH0gPSBoeXBlcmFwcFxuLyogQGpzeCBoICovXG5cbmNvbnN0IHN0YXRlID0ge1xuXHR1c2VyOiB7XG5cdFx0YXZhdGFyVVJMOlxuXHRcdFx0XCJkYXRhOmltYWdlL2dpZjtiYXNlNjQsUjBsR09EbGhBUUFCQUlBQUFBQUFBUC8vL3lINUJBRUFBQUFBTEFBQUFBQUJBQUVBQUFJQlJBQTdcIlxuXHR9LFxuXHRyb29tOiB7fSxcblx0bWVzc2FnZXM6IFtdXG59XG5cbmNvbnN0IGFjdGlvbnMgPSB7XG5cdHNldFVzZXI6IHVzZXIgPT4gKHsgdXNlciB9KSxcblx0c2V0Um9vbTogcm9vbSA9PiAoeyByb29tIH0pLFxuXHRhZGRNZXNzYWdlOiBwYXlsb2FkID0+ICh7IG1lc3NhZ2VzIH0pID0+ICh7XG5cdFx0bWVzc2FnZXM6IFtwYXlsb2FkLCAuLi5tZXNzYWdlc11cblx0fSlcbn1cblxuY29uc3QgeyBhZGRNZXNzYWdlLCBzZXRVc2VyLCBzZXRSb29tIH0gPSBhcHAoXG5cdHN0YXRlLFxuXHRhY3Rpb25zLFxuXHR2aWV3LFxuXHRkb2N1bWVudC5ib2R5XG4pXG5cblxuXG5cbiJdLCJzb3VyY2VSb290IjoiIn0=