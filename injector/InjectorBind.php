<?
namespace injector;

interface InjectorBind{
	function provide();
	function isSingleton();
	function isFinal();
}

